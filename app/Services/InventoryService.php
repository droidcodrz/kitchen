<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Project;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    private function requiredQuantitiesForProject(Project $project): array
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

            // Validate stock is sufficient for every material before deducting any of them
            foreach ($required as $materialId => $qty) {
                $material = $materials->get($materialId);

                if (!$material || $material->stock_quantity < $qty) {
                    $available = $material->stock_quantity ?? 0;
                    $name = $material->name ?? "material #{$materialId}";

                    throw new \InvalidArgumentException(
                        "Cannot start production: insufficient stock for {$name}. Available: {$available}, needed: {$qty}."
                    );
                }
            }

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
