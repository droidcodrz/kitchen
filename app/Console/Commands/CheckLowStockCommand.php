<?php

namespace App\Console\Commands;

use App\Models\AlertConfiguration;
use App\Models\Role;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    protected $signature = 'inventory:check-low-stock';

    protected $description = 'Check for low stock items and notify relevant users';

    public function handle(InventoryService $inventoryService): int
    {
        $lowStockItems = $inventoryService->getLowStockItems();

        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return Command::SUCCESS;
        }

        $config = AlertConfiguration::where('alert_type', 'low_stock')
            ->where('is_enabled', true)
            ->first();

        if (!$config) {
            $this->info('Low stock alerts are disabled.');
            return Command::SUCCESS;
        }

        $usersQuery = User::where('status', 'active');
        if (!empty($config->notify_roles)) {
            $roleIds = Role::whereIn('slug', $config->notify_roles)->pluck('id');
            if ($roleIds->isNotEmpty()) {
                $usersQuery->whereIn('role_id', $roleIds);
            }
        }
        $users = $usersQuery->get();

        if ($users->isEmpty()) {
            $this->info('No users to notify.');
            return Command::SUCCESS;
        }

        $notified = 0;

        foreach ($lowStockItems as $item) {
            foreach ($users as $user) {
                // Skip if an unread notification for this item already exists
                $exists = $user->unreadNotifications()
                    ->where('type', LowStockNotification::class)
                    ->whereJsonContains('data->inventory_item_id', $item->id)
                    ->exists();

                if (!$exists) {
                    $user->notify(new LowStockNotification($item));
                    $notified++;
                }
            }
        }

        $this->info("Sent {$notified} notifications for {$lowStockItems->count()} low stock items to {$users->count()} users.");

        return Command::SUCCESS;
    }
}
