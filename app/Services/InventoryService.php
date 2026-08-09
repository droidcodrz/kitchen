<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Project;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function __construct(
        protected AlertNotifier $alertNotifier
    ) {
    }

    /**
     * Adjust inventory stock and create a transaction record.
     */
    public function adjustStock(
        InventoryItem $item,
        string $type,
        float $quantity,
        ?string $notes = null
    ): InventoryTransaction {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative.');
        }

        if (!in_array($type, ['addition', 'deduction', 'adjustment'], true)) {
            throw new InvalidArgumentException('Invalid stock adjustment type.');
        }

        return DB::transaction(function () use ($item, $type, $quantity, $notes) {
            $item = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($item->id);

            if ($type === 'addition') {
                $item->increment('stock_quantity', $quantity);

                $item->update([
                    'last_added_quantity' => $quantity,
                    'last_added_at' => now(),
                ]);
            }

            if ($type === 'deduction') {
                if ($item->stock_quantity < $quantity) {
                    throw new InvalidArgumentException(
                        "Insufficient stock. Available: {$item->stock_quantity}"
                    );
                }

                $item->decrement('stock_quantity', $quantity);
            }

            if ($type === 'adjustment') {
                $item->update([
                    'stock_quantity' => $quantity,
                ]);
            }

            $transaction = InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantity,
                'notes' => $notes,
                'performed_by' => Auth::id(),
            ]);

            $item->refresh();

            if ($item->is_low_stock) {
                $this->notifyLowStock($item);
            }

            return $transaction;
        });
    }

    /**
     * Reserve all required materials for a project.
     */
    public function reserveForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $requirements = $this->getProjectMaterialRequirements($project);

            foreach ($requirements as $materialId => $requiredQuantity) {
                $material = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($materialId);

                $availableQuantity = $material->stock_quantity - $material->reserved_quantity;

                if ($availableQuantity < $requiredQuantity) {
                    throw new InvalidArgumentException(
                        "Insufficient available stock for {$material->name}. "
                        . "Available: {$availableQuantity}, needed: {$requiredQuantity}."
                    );
                }

                $material->increment('reserved_quantity', $requiredQuantity);

                InventoryTransaction::create([
                    'inventory_item_id' => $material->id,
                    'type' => 'reservation',
                    'quantity' => $requiredQuantity,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Reserved for project: {$project->name}",
                    'performed_by' => Auth::id(),
                ]);
            }
        });
    }

    /**
     * Release materials reserved for a project.
     */
    public function releaseForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $requirements = $this->getProjectMaterialRequirements($project);

            foreach ($requirements as $materialId => $requiredQuantity) {
                $material = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($materialId);

                $releasedQuantity = min(
                    (float) $material->reserved_quantity,
                    $requiredQuantity
                );

                if ($releasedQuantity <= 0) {
                    continue;
                }

                $material->decrement('reserved_quantity', $releasedQuantity);

                InventoryTransaction::create([
                    'inventory_item_id' => $material->id,
                    'type' => 'release',
                    'quantity' => $releasedQuantity,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Released from project: {$project->name}",
                    'performed_by' => Auth::id(),
                ]);
            }
        });
    }

    /**
     * Consume reserved materials when production starts.
     */
    public function consumeForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $requirements = $this->getProjectMaterialRequirements($project);

            foreach ($requirements as $materialId => $requiredQuantity) {
                $material = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($materialId);

                if ($material->stock_quantity < $requiredQuantity) {
                    throw new InvalidArgumentException(
                        "Cannot start production: insufficient stock for {$material->name}. "
                        . "Available: {$material->stock_quantity}, needed: {$requiredQuantity}."
                    );
                }

                $material->decrement('stock_quantity', $requiredQuantity);

                $newReservedQuantity = max(
                    0,
                    (float) $material->reserved_quantity - $requiredQuantity
                );

                $material->update([
                    'reserved_quantity' => $newReservedQuantity,
                ]);

                InventoryTransaction::create([
                    'inventory_item_id' => $material->id,
                    'type' => 'deduction',
                    'quantity' => $requiredQuantity,
                    'reference_type' => Project::class,
                    'reference_id' => $project->id,
                    'notes' => "Consumed for project: {$project->name}",
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
     * Return combined material quantities required for a project.
     *
     * @return array<int, float>
     */
    private function getProjectMaterialRequirements(Project $project): array
    {
        $project->load('products.requiredMaterials');

        $requirements = [];

        foreach ($project->products as $product) {
            $projectQuantity = (float) ($product->pivot->quantity ?? 1);

            foreach ($product->requiredMaterials as $material) {
                $requiredQuantity = (float) ($material->pivot->quantity_required ?? 0)
                    * $projectQuantity;

                if ($requiredQuantity <= 0) {
                    continue;
                }

                $requirements[$material->id] = ($requirements[$material->id] ?? 0)
                    + $requiredQuantity;
            }
        }

        return $requirements;
    }

    /**
     * Get active inventory items below their minimum stock level.
     */
    public function getLowStockItems(): Collection
    {
        return InventoryItem::query()
            ->whereColumn('stock_quantity', '<', 'minimum_stock_level')
            ->where('is_active', true)
            ->with(['vendor', 'storageLocation'])
            ->orderByRaw('stock_quantity - minimum_stock_level ASC')
            ->get();
    }

    /**
     * Notify recipients about all currently low-stock items.
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
     * Send a low-stock notification, avoiding duplicate unread notifications.
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