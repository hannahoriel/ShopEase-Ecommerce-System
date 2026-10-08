<?php

namespace App\Models\Buyer;

use App\Models\Admin\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BuyerNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'order_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public static function createForOrder(
        Order $order,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): self {
        return self::query()->create([
            'buyer_id' => $order->buyer_id,
            'order_id' => $order->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => array_merge([
                'order_number' => $order->order_number,
            ], $data),
        ]);
    }

    public static function createForOrderStatus(Order $order, string $status): self
    {
        $title = match ($status) {
            'pending', 'new' => 'Order received',
            'preparing' => 'Order is being processed',
            'to_ship' => 'Order is ready to ship',
            'in_transit' => 'Your package is in transit',
            'out_for_delivery' => 'Your package is out for delivery',
            'delivered', 'completed' => 'Order delivered successfully',
            'cancelled' => 'Order cancelled',
            default => 'Order status updated',
        };
        $type = match ($status) {
            'in_transit', 'out_for_delivery' => 'shipping',
            'delivered', 'completed' => 'delivered',
            default => 'order',
        };

        return self::createForOrder(
            $order,
            $type,
            $title,
            'Your order '.$order->order_number.' is now '.Str::headline($status).'.',
            ['status' => $status]
        );
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
