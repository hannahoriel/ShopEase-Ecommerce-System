<?php

namespace App\Notifications;

use App\Models\Admin\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $itemCount = $this->order->relationLoaded('items')
            ? $this->order->items->count()
            : $this->order->items()->count();

        return [
            'title' => 'New Order Received',
            'message' => 'Order '.$this->order->order_number.' was placed for ₱'.number_format((float) $this->order->total, 2).'. '.$itemCount.' item'.($itemCount !== 1 ? 's' : '').' to prepare.',
            'type' => 'new_order',
            'category' => 'new_orders',
            'preference' => 'new_orders',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => (float) $this->order->total,
            'item_count' => $itemCount,
            'buyer_name' => $this->order->relationLoaded('buyer')
                ? ($this->order->buyer?->name ?? 'Buyer')
                : 'Buyer',
            'url' => '/seller/order-status',
        ];
    }
}
