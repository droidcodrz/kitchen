<?php

namespace App\Console\Commands;

use App\Models\InventoryItem;
use App\Models\Project;
use App\Services\InventoryService;
use Illuminate\Console\Command;

/**
 * Rebuilds every item's reserved_quantity from the projects that actually
 * hold a reservation.
 *
 * reserved_quantity is maintained incrementally - reserved on confirmation,
 * released on delete or when a project stops being confirmed. Any interruption
 * in that sequence leaves the running total wrong, and because it is a running
 * total the error persists rather than correcting itself. This recomputes the
 * figure from scratch instead of adjusting it.
 *
 * Only confirmed projects hold reservations: a draft never took one, and
 * in_production and later have already consumed their stock outright. Stock
 * quantities are never touched here, only reservations.
 */
class RecalculateReservations extends Command
{
    protected $signature = 'inventory:recalculate-reservations {--dry-run : Show what would change without writing anything}';

    protected $description = 'Rebuild reserved quantities from confirmed projects, correcting any drift';

    public function handle(InventoryService $inventoryService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $expected = [];

        Project::where('status', 'confirmed')
            ->with('products.requiredMaterials', 'inventoryItems')
            ->chunk(100, function ($projects) use ($inventoryService, &$expected) {
                foreach ($projects as $project) {
                    foreach ($inventoryService->requiredQuantitiesForProject($project) as $itemId => $quantity) {
                        $expected[$itemId] = ($expected[$itemId] ?? 0) + $quantity;
                    }
                }
            });

        $rows = [];
        $corrected = 0;

        foreach (InventoryItem::all() as $item) {
            $current = (float) $item->reserved_quantity;
            $target = (float) ($expected[$item->id] ?? 0);

            if (abs($current - $target) < 0.0001) {
                continue;
            }

            $rows[] = [$item->name, $item->sku, $current, $target];
            $corrected++;

            if (!$dryRun) {
                $item->update(['reserved_quantity' => $target]);
            }
        }

        if ($corrected === 0) {
            $this->info('All reserved quantities already match the confirmed projects. Nothing to correct.');

            return self::SUCCESS;
        }

        $this->table(['Item', 'SKU', 'Was', 'Now'], $rows);

        $this->info($dryRun
            ? "{$corrected} item(s) would be corrected. Re-run without --dry-run to apply."
            : "{$corrected} item(s) corrected.");

        return self::SUCCESS;
    }
}
