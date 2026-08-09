<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    protected $signature = 'inventory:check-low-stock';

    protected $description = 'Check for low stock items and notify relevant users';

    public function handle(InventoryService $inventoryService): int
    {
         $count = $inventoryService->checkAndNotifyLowStock();

        if ($count === 0) {
            $this->info('No low stock items found.');
        } else {
             $this->info("Checked {$count} low stock item(s).");
        }

        return Command::SUCCESS;
    }
}
