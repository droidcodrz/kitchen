<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected InventoryItem $item,
        protected bool $viaEmail = false
    ) {}

    public function via(object $notifiable): array
    {
        // An always_notify_emails address isn't a User, so it has nowhere to
        // store a database notification - mail only for those.
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
            return ['mail'];
        }

        return array_filter(['database', $this->viaEmail ? 'mail' : null]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->item->name)
            ->line(sprintf(
                '%s (SKU: %s) has %s %s remaining. Minimum level is %s.',
                $this->item->name,
                $this->item->sku,
                number_format($this->item->stock_quantity, 0),
                $this->item->unit_of_measure ?? 'units',
                number_format($this->item->minimum_stock_level, 0)
            ))
            ->action('View Inventory Item', route('inventory.show', $this->item));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'title' => 'Low Stock Alert: ' . $this->item->name,
            'message' => sprintf(
                '%s (SKU: %s) has %s %s remaining. Minimum level is %s.',
                $this->item->name,
                $this->item->sku,
                number_format($this->item->stock_quantity, 0),
                $this->item->unit_of_measure ?? 'units',
                number_format($this->item->minimum_stock_level, 0)
            ),
            'description' => sprintf(
                '%s (SKU: %s) has %s %s remaining. Minimum level is %s.',
                $this->item->name,
                $this->item->sku,
                number_format($this->item->stock_quantity, 0),
                $this->item->unit_of_measure ?? 'units',
                number_format($this->item->minimum_stock_level, 0)
            ),
            'inventory_item_id' => $this->item->id,
            'url' => route('inventory.show', $this->item),
        ];
    }
}
