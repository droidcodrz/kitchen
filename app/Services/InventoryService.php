<?php

namespace App\Services;

use App\Models\AlertConfiguration;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
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
     * Reserve materials needed for a project based on its products.
     */
    public function reserveForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project->load('products.requiredMaterials');

            foreach ($project->products as $product) {
                $projectQuantity = $product->pivot->quantity ?? 1;

                foreach ($product->requiredMaterials as $material) {
                    $requiredQty = ($material->pivot->quantity_required ?? 0) * $projectQuantity;

                    if ($requiredQty > 0) {
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
                }
            }
        });
    }

    /**
     * Release reserved materials for a project.
     */
    public function releaseForProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project->load('products.requiredMaterials');

            foreach ($project->products as $product) {
                $projectQuantity = $product->pivot->quantity ?? 1;

                foreach ($product->requiredMaterials as $material) {
                    $requiredQty = ($material->pivot->quantity_required ?? 0) * $projectQuantity;

                    if ($requiredQty > 0) {
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
                }
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
            $project->load('products.requiredMaterials');

            // Aggregate total required quantity per material across all products on the project
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
     * Send low stock notifications to relevant users.
     */
    private function notifyLowStock(InventoryItem $item): void
    {
        $config = AlertConfiguration::where('alert_type', 'low_stock')
            ->where('is_enabled', true)
            ->first();

        if (!$config) {
            return;
        }

        $usersQuery = User::where('status', 'active');
        if (!empty($config->notify_roles)) {
            $roleIds = Role::whereIn('slug', $config->notify_roles)->pluck('id');
            if ($roleIds->isNotEmpty()) {
                $usersQuery->whereIn('role_id', $roleIds);
            }
        }

        foreach ($usersQuery->get() as $user) {
            $exists = $user->unreadNotifications()
                ->where('type', LowStockNotification::class)
                ->whereJsonContains('data->inventory_item_id', $item->id)
                ->exists();

            if (!$exists) {
                $user->notify(new LowStockNotification($item));
            }
        }
    }
}
