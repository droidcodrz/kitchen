<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Exceptions\InsufficientStockException;
use App\Models\Project;
use App\Notifications\InsufficientStockNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function __construct(
        protected AlertNotifier $alertNotifier
    ) {}

    /**
     * Adjust the stock of an inventory item and record a transaction.
     */
    public function adjustStock(
        InventoryItem $item,
        string $type,
        float $quantity,
        ?string $notes = null
    ): InventoryTransaction {
        return DB::transaction(function () use ($item, $type, $quantity, $notes) {
            switch ($type) {
                case 'addition':
                    $item->increment('stock_quantity', $quantity);
                    $item->update([
                        'last_added_quantity' => $quantity,
                        'last_added_at' => now(),
                    ]);
                    break;

                case 'deduction':
                    if ($item->stock_quantity < $quantity) {
                        throw new \InvalidArgumentException(
                            'Insufficient stock. Available: ' . $item->stock_quantity
                        );
                    }
                    $item->decrement('stock_quantity', $quantity);
                    break;

                case 'adjustment':
                    // Direct set to quantity value
                    $item->update(['stock_quantity' => $quantity]);
                    break;
            }

            $transaction = InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantity,
                'notes' => $notes,
                'performed_by' => Auth::id(),
            ]);

            // Check for low stock after adjustment
            $item->refresh();
            if ($item->is_low_stock) {
                $this->notifyLowStock($item);
            }

            return $transaction;
        });
    }

    /**
     * Aggregate total required quantity per inventory item across a project's
     * manufactured products (via their BOM) AND any raw inventory items
     * attached directly to the project. Same item counted both ways just adds up.
     */
    public function requiredQuantitiesForProject(Project $project): array
    {
        $project->load('products.requiredMaterials', 'inventoryItems');

        $required = [];

        foreach ($project->products as $product) {
            $projectQuantity = $product->pivot->quantity ?? 1;

            foreach ($product->requiredMaterials as $material) {
                $requiredQty = ($material->pivot->quantity_required ?? 0) * $projectQuantity;

                if ($requiredQty > 0) {
                    $required[$material->id] = ($required[$material->id] ?? 0) + $requiredQty;
                }
            }
        }

        foreach ($project->inventoryItems as $item) {
            $requiredQty = $item->pivot->quantity ?? 0;

            if ($requiredQty > 0) {
                $required[$item->id] = ($required[$item->id] ?? 0) + $requiredQty;
            }
        }

        return $required;
    }

    /**
     * Reserve materials needed for a project based on its products' BOM and
     * any inventory items attached directly to the project.
     */
    public function reserveForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $required = $this->requiredQuantitiesForProject($project);

            if (empty($required)) {
                return;
            }

            $materials = InventoryItem::whereIn('id', array_keys($required))->get()->keyBy('id');

            foreach ($required as $materialId => $requiredQty) {
                $material = $materials->get($materialId);

                if (!$material) {
                    continue;
                }

                $material->increment('reserved_quantity', $requiredQty);

                InventoryTransaction::create([
                    'inventory_item_id' => $material->id,
                    'type' => 'reservation',
                    'quantity' => $requiredQty,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Reserved for project: {$project->name}",
                    'performed_by' => Auth::id(),
                ]);
            }
        });
    }

    /**
     * Release reserved materials for a project.
     */
    public function releaseForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $required = $this->requiredQuantitiesForProject($project);

            if (empty($required)) {
                return;
            }

            $materials = InventoryItem::whereIn('id', array_keys($required))->get()->keyBy('id');

            foreach ($required as $materialId => $requiredQty) {
                $material = $materials->get($materialId);

                if (!$material) {
                    continue;
                }

                $newReserved = max(0, $material->reserved_quantity - $requiredQty);
                $material->update(['reserved_quantity' => $newReserved]);

                InventoryTransaction::create([
                    'inventory_item_id' => $material->id,
                    'type' => 'release',
                    'quantity' => $requiredQty,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Released from project: {$project->name}",
                    'performed_by' => Auth::id(),
                ]);
            }
        });
    }

    /**
     * Consume (deduct) reserved materials for a project once production starts.
     * Converts a prior reservation into an actual stock deduction.
     *
     * @throws \InvalidArgumentException if any required material has insufficient stock
     */
    public function consumeForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $required = $this->requiredQuantitiesForProject($project);

            if (empty($required)) {
                return;
            }

            $materials = InventoryItem::whereIn('id', array_keys($required))->get()->keyBy('id');

            // Check every material before deducting any of them, and report all
            // the short ones at once - fixing them one failed save at a time is
            // needless when the whole shortfall is already known here.
            $this->guardAgainstShortages($required, $materials, $project, 'Starting production');

            foreach ($required as $materialId => $qty) {
                $material = $materials->get($materialId);

                $material->decrement('stock_quantity', $qty);
                $material->update([
                    'reserved_quantity' => max(0, $material->reserved_quantity - $qty),
                ]);

                InventoryTransaction::create([
                    'inventory_item_id' => $materialId,
                    'type' => 'deduction',
                    'quantity' => $qty,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Consumed for project: {$project->name}",
                    'performed_by' => Auth::id(),
                ]);

                // Check for low stock after consumption
                $material->refresh();
                if ($material->is_low_stock) {
                    $this->notifyLowStock($material);
                }
            }
        });
    }

    /**
     * Move already-consumed stock to match a changed bill of materials.
     *
     * Once a project is in production its materials have been deducted, not
     * reserved, so editing what it contains has to settle the difference:
     * take the extra for anything added, hand back anything removed. Pass the
     * quantities the project required *before* the change - the current ones
     * are read from the project as it now stands.
     *
     * @param array<int, float> $previousRequired  material id => quantity
     * @throws InsufficientStockException if stock cannot cover an increase
     */
    public function adjustConsumptionForProject(Project $project, array $previousRequired): void
    {
        DB::transaction(function () use ($project, $previousRequired) {
            $current = $this->requiredQuantitiesForProject($project);

            $deltas = [];
            foreach (array_unique(array_merge(array_keys($previousRequired), array_keys($current))) as $materialId) {
                $delta = ($current[$materialId] ?? 0) - ($previousRequired[$materialId] ?? 0);

                if (abs($delta) > 0.0001) {
                    $deltas[$materialId] = $delta;
                }
            }

            if (empty($deltas)) {
                return;
            }

            $materials = InventoryItem::whereIn('id', array_keys($deltas))->get()->keyBy('id');

            // Only the increases need covering; decreases give stock back.
            $increases = array_filter($deltas, fn ($d) => $d > 0);
            $this->guardAgainstShortages($increases, $materials, $project, 'Adding materials to a project already in production');

            foreach ($deltas as $materialId => $delta) {
                $material = $materials->get($materialId);

                if (!$material) {
                    continue;
                }

                if ($delta > 0) {
                    $material->decrement('stock_quantity', $delta);
                } else {
                    $material->increment('stock_quantity', abs($delta));
                }

                InventoryTransaction::create([
                    'inventory_item_id' => $materialId,
                    'type' => $delta > 0 ? 'deduction' : 'addition',
                    'quantity' => abs($delta),
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => $delta > 0
                        ? "Consumed for project: {$project->name} (materials added after production started)"
                        : "Returned from project: {$project->name} (materials removed after production started)",
                    'performed_by' => Auth::id(),
                ]);

                $material->refresh();
                if ($material->is_low_stock) {
                    $this->notifyLowStock($material);
                }
            }
        });
    }

    /**
     * Throw if any required quantity exceeds what the item actually has,
     * alerting the configured recipients first. The action is refused, so the
     * alert is a prompt to restock rather than a record of a deduction.
     *
     * @param array<int, float> $required
     * @param \Illuminate\Support\Collection<int, InventoryItem> $materials
     * @throws InsufficientStockException
     */
    private function guardAgainstShortages(array $required, $materials, Project $project, string $action): void
    {
        $shortages = [];

        foreach ($required as $materialId => $needed) {
            $material = $materials->get($materialId);

            if (!$material || $material->stock_quantity < $needed) {
                $shortages[] = [
                    'name' => $material->name ?? "Material #{$materialId}",
                    'sku' => $material->sku ?? null,
                    'available' => (float) ($material->stock_quantity ?? 0),
                    'needed' => (float) $needed,
                    'unit' => $material->unit_of_measure ?? null,
                ];
            }
        }

        if (empty($shortages)) {
            return;
        }

        // Deliberately does not alert here: this runs inside a transaction
        // that is about to roll back, and a database notification written
        // inside it would be rolled back with everything else. The callers
        // alert once the rollback has happened.
        throw new InsufficientStockException($shortages, $action);
    }

    /**
     * Alert the configured recipients that an action was blocked by short stock.
     *
     * Call this only once the failed action's transaction has rolled back -
     * from the controller handling the exception, not from inside the service.
     * A database notification written inside the doomed transaction is rolled
     * back with it, which silently loses the in-app alert while the email,
     * being non-transactional, still goes out.
     *
     * Wrapped so a mail failure cannot mask the shortage itself, which is what
     * the user actually needs to hear about.
     */
    public function notifyInsufficientStock(array $shortages, Project $project, ?string $action = null): void
    {
        try {
            $this->alertNotifier->notify(
                'insufficient_stock',
                fn (bool $viaEmail) => new InsufficientStockNotification($shortages, $project, $action, $viaEmail)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send insufficient stock alert', [
                'project_id' => $project->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get all inventory items that are below their minimum stock level.
     */
    public function getLowStockItems(): Collection
    {
        return InventoryItem::whereColumn('stock_quantity', '<', 'minimum_stock_level')
            ->where('is_active', true)
            ->with(['vendor', 'storageLocation'])
            ->orderByRaw('stock_quantity - minimum_stock_level ASC')
            ->get();
    }

    /**
     * Check every currently low-stock item and notify relevant users.
     * Used by the scheduled sweep, in addition to the real-time checks
     * that run after stock adjustments/consumption above.
     */
    public function checkAndNotifyLowStock(): int
    {
        $items = $this->getLowStockItems();

        foreach ($items as $item) {
            $this->notifyLowStock($item);
        }

        return $items->count();
    }

    /**
     * Send low stock notifications to relevant users.
     */
    private function notifyLowStock(InventoryItem $item): void
    {
        $this->alertNotifier->notify(
            'low_stock',
            fn (bool $viaEmail) => new LowStockNotification($item, $viaEmail),
            fn ($user) => $user->unreadNotifications()
                ->where('type', LowStockNotification::class)
                ->whereJsonContains('data->inventory_item_id', $item->id)
                ->exists()
        );
    }
}
