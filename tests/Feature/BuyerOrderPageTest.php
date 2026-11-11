<?php

namespace Tests\Feature;

use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\Seller\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerOrderPageTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create(['name'=>'Order Page User','email'=>uniqid().'@example.com','password'=>'Password123!','role'=>$role,'registration_status'=>'active']);
    }

    public function test_order_page_uses_all_real_items_and_preserves_order_information(): void
    {
        $buyer=$this->user(User::ROLE_BUYER);
        $sellerUser=$this->user(User::ROLE_SELLER);
        $seller=Seller::create(['user_id'=>$sellerUser->id,'store_name'=>'Actual Shop','registration_status'=>'active']);
        $order=Order::create(['buyer_id'=>$buyer->id,'seller_id'=>$seller->id,'order_number'=>'REAL-ORDER-ONE',
            'status'=>'pending','total'=>350,'shipping_fee'=>50,'voucher_discount'=>20,'voucher_code'=>'REAL20',
            'delivery_name'=>'Actual Recipient','delivery_phone'=>'09171234567','delivery_address'=>'Actual Delivery Address',
            'payment_method'=>'ShopEase Wallet']);
        foreach (['Real Glass','Real Plate'] as $name) {
            $product=Product::create(['seller_id'=>$seller->id,'name'=>$name,'price'=>160,'stock_quantity'=>4,'status'=>'active','sku'=>uniqid('SKU'),'is_fragile'=>true]);
            $order->items()->create(['product_id'=>$product->id,'product_name'=>$name,'quantity'=>1,'unit_price'=>160,
                'variation'=>'Large','color'=>'Blue','size'=>'M','is_fragile'=>true]);
        }
        $this->actingAs($buyer)->getJson('/api/v1/buyer/orders')->assertOk()
            ->assertJsonPath('data.0.tracking_events.0.description','Processing')
            ->assertJsonPath('data.0.tracking_events.1.description','Order Placed')
            ->assertJsonPath('data.0.tracking_events.0.created_at',$order->created_at->toISOString());
        $this->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertOk()->assertSee('Order Placed')
            ->assertDontSee('No tracking updates have been recorded yet.')->assertSee('Tracking number:');
        $this->actingAs($sellerUser)->patchJson('/api/v1/seller/orders/'.$order->id.'/status',[
            'status'=>'preparing','notes'=>'Seller is carefully packing the glass.',
        ])->assertOk();
        $this->patchJson('/api/v1/seller/orders/'.$order->id.'/status',[
            'status'=>'to_ship','notes'=>'Parcel is ready for courier pickup.',
        ])->assertOk();
        $this->actingAs($buyer)->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertOk()
            ->assertSee('Processing')->assertSee('To Ship')->assertSee('Seller update')
            ->assertSee('Seller is carefully packing the glass.')->assertSee('Parcel is ready for courier pickup.')
            ->assertSeeInOrder(['id="voTimeline"','To Ship','Parcel is ready for courier pickup.','Processing','Seller is carefully packing the glass.','Order Placed'],false);
        $order->update(['status'=>'delivered']);
        $order->statusHistory()->create(['from_status'=>'in_transit','to_status'=>'delivered','created_at'=>now()->subHour()]);
        $this->actingAs($buyer)->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertOk()
            ->assertSee('Real Glass')->assertSee('Real Plate')->assertSee('Actual Recipient')->assertSee('Actual Delivery Address')
            ->assertSee('ShopEase Wallet')->assertSee('REAL20')->assertSee('Fragile')->assertSee('Order verification')
            ->assertSee('Report a Problem')->assertSee('Large · Blue · M')
            ->assertDontSee('Preview using sample order information')->assertDontSee('Demo only');
        $this->getJson('/api/v1/buyer/orders')->assertOk()->assertJsonPath('data.0.order_number','REAL-ORDER-ONE')
            ->assertJsonCount(2,'data.0.items')->assertJsonPath('data.0.items.0.id',$order->items()->first()->id);
        $this->patchJson('/api/v1/buyer/orders/'.$order->id.'/confirm-received')->assertOk();
        $this->assertSame('completed',$order->fresh()->status);
        $productId=$order->items()->first()->product_id;
        $this->postJson('/api/v1/buyer/products/'.$productId.'/reviews',[
            'rating'=>4,'body'=>'Actual review saved from the order page','is_anonymous'=>true,
        ])->assertCreated();
        $this->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertOk()->assertSee('Actual review saved from the order page');
        $this->get('/buyer/my-purchases')->assertOk()->assertDontSee('id="buyerOrderDetailsModal"',false);
        $this->actingAs($this->user(User::ROLE_BUYER))->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertNotFound();
        $this->actingAs($buyer)->get('/buyer/view-order?order=ORD-DEMO-1801')->assertNotFound();
        $this->get('/buyer/view-order')->assertNotFound();
        $this->actingAs($sellerUser)->get('/buyer/view-order?order=REAL-ORDER-ONE')->assertForbidden();
    }
}
