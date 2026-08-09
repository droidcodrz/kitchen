<?php

 

namespace App\Notifications;

 

use App\Models\InventoryItem;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;

 

class InventoryItemAddedNotification extends Notification implements ShouldQueue

{

    use Queueable;

 

    public function __construct(

        protected InventoryItem $item,

        protected bool $viaEmail = false

    ) {}

 

    public function via(object $notifiable): array

    {
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
    return ['mail'];
}

        return array_filter(['database', $this->viaEmail ? 'mail' : null]);

    }

 

    public function toMail(object $notifiable): MailMessage

    {

        return (new MailMessage)

            ->subject('New Inventory Item Added: ' . $this->item->name)

            ->line($this->message())

            ->action('View Inventory Item', route('inventory.show', $this->item));

    }

 

    public function toArray(object $notifiable): array

    {

        return [

            'type' => 'new_item',

            'title' => 'New Inventory Item: ' . $this->item->name,

            'message' => $this->message(),

            'description' => $this->message(),

            'inventory_item_id' => $this->item->id,

            'url' => route('inventory.show', $this->item),

        ];

    }

 

    private function message(): string

    {

        return sprintf(

            '%s (SKU: %s) was added to inventory with an opening stock of %s %s.',

            $this->item->name,

            $this->item->sku,

            number_format($this->item->stock_quantity, 0),

            $this->item->unit_of_measure ?? 'units'

        );

    }

}