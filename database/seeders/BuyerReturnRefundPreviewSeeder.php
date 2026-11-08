<?php

namespace Database\Seeders;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Temporary UI fixture. Explicitly run this class; never included in DatabaseSeeder.
 * Cleanup later targets ORDER_NUMBER and its related records, not other orders. */
class BuyerReturnRefundPreviewSeeder extends Seeder
{
    public const ORDER_NUMBER = 'SE-DEMO-RETURN-REFUND-0001';
    public const BUYER_EMAIL = 'buyer.route@shopease.test';

    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) throw new \RuntimeException('Preview orders are for local/testing use only.');
        if (Order::where('order_number', self::ORDER_NUMBER)->exists()) {
            $this->command?->info('Preview order already exists; its current return/refund progress was preserved.');
            return;
        }
        $buyer = User::where('email', self::BUYER_EMAIL)->where('role', User::ROLE_BUYER)->firstOrFail();
        $source = Order::where('buyer_id', $buyer->id)->whereHas('items')->with(['items', 'seller', 'shipment'])->latest('id')->firstOrFail();
        DB::transaction(function () use ($buyer, $source): void {
            $placed = now()->subDays(3);
            $delivered = now()->subHour();
            $subtotal = $source->items->sum(fn ($item) => $item->quantity * (float)$item->unit_price);
            // UI demo only: no stock reservation, payment, commission or notices.
            $order = Order::withoutEvents(fn () => Order::create([
                'buyer_id'=>$buyer->id, 'seller_id'=>$source->seller_id, 'logistics_id'=>$source->logistics_id,
                'order_number'=>self::ORDER_NUMBER, 'status'=>'delivered',
                'total'=>$subtotal + (float)$source->shipping_fee, 'shipping_fee'=>$source->shipping_fee,
                'voucher_discount'=>0, 'voucher_code'=>null, 'payment_method'=>'Cash on Delivery',
                'delivery_name'=>$source->delivery_name, 'delivery_phone'=>$source->delivery_phone,
                'delivery_address'=>$source->delivery_address,
            ]));
            $order->forceFill(['created_at'=>$placed, 'updated_at'=>$delivered])->saveQuietly();
            foreach ($source->items as $item) {
                $order->items()->create($item->only(['product_id','product_name','is_fragile','variation','color','size','quantity','unit_price']));
            }
            Shipment::create([
                'order_id'=>$order->id, 'tracking_number'=>'SE-DEMO-RR-0001',
                'courier'=>$source->shipment?->courier ?: 'ShopEase Demo Logistics',
                'estimated_delivery'=>$delivered->toDateString(), 'shipping_fee'=>$source->shipping_fee,
                'picked_up_at'=>$placed->copy()->addDay(), 'delivered_at'=>$delivered,
                'current_location'=>'Delivered to buyer — return/refund UI demo',
                'location_updated_at'=>$delivered,
            ]);
            $previous='pending';
            foreach ([
                ['preparing',$placed->copy()->addHours(2),$source->seller?->user_id,'Demo: seller prepared the parcel.'],
                ['to_ship',$placed->copy()->addHours(4),$source->seller?->user_id,'Demo: parcel ready for pickup.'],
                ['in_transit',$placed->copy()->addDay(),null,'Demo: parcel in transit.'],
                ['out_for_delivery',$delivered->copy()->subHours(3),null,'Demo: parcel out for delivery.'],
                ['delivered',$delivered,null,'Demo: delivered order for Return / Refund UI preview.'],
            ] as [$status,$time,$actor,$notes]) {
                $history=$order->statusHistory()->make(['from_status'=>$previous,'to_status'=>$status,'changed_by'=>$actor,'notes'=>$notes]);
                $history->forceFill(['created_at'=>$time,'updated_at'=>$time])->save();
                $previous=$status;
            }
        });
        $this->command?->info('Delivered preview order: '.self::ORDER_NUMBER.' | Buyer: '.self::BUYER_EMAIL);
    }
}
