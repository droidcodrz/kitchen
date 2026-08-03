<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected InventoryItem $item
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'title' => 'Low Stock Alert: ' . $this->item->name,
            'description' => sprintf(
                '%s (SKU: %s) has %s %s remaining. Minimum level is %s.',
                $this->item->name,
                $this->item->sku,
                number_format($this->item->stock_quantity, 0),
                $this->item->unit_of_measure ?? 'units',
                number_format($this->item->minimum_stock_level, 0)
            ),
            'inventory_item_id' => $this->item->id,
        ];
    }
}
