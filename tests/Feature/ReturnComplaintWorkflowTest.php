<?php

namespace Tests\Feature;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Mail::fake();Storage::fake('public');
        $user=fn($role)=>User::create(['name'=>'Workflow '.$role,'email'=>uniqid().'@example.com','password'=>'Password123!','role'=>$role,'registration_status'=>'active']);
        $buyer=$user('buyer');$sellerUser=$user('seller');
        $buyer->update(['profile_photo_path'=>'profiles/buyer.jpg']);$sellerUser->update(['profile_photo_path'=>'profiles/seller.jpg']);
        $seller=Seller::create(['user_id'=>$sellerUser->id,'store_name'=>'Workflow Shop','registration_status'=>'active']);
        $order=Order::create(['buyer_id'=>$buyer->id,'seller_id'=>$seller->id,'order_number'=>uniqid('TEST-'),'status'=>'delivered',
            'total'=>320,'shipping_fee'=>50,'voucher_discount'=>30,'payment_method'=>'Cash on Delivery',
            'delivery_name'=>'Buyer','delivery_phone'=>'09170000000','delivery_address'=>'Test Address']);
        $order->shipment()->create(['tracking_number'=>uniqid('TRACK-'),'courier'=>'Test Courier','delivered_at'=>now()->subHour()]);
        foreach (['First Product'=>100,'Selected Product'=>200] as $name=>$price) {
            $product=Product::create(['seller_id'=>$seller->id,'name'=>$name,'price'=>$price,'sku'=>uniqid('SKU-'),'status'=>'active','stock_quantity'=>5,'photos'=>[$name==='Selected Product'?'products/selected.jpg':'products/first.jpg']]);
            $order->items()->create(['product_id'=>$product->id,'product_name'=>$name,'unit_price'=>$price,'quantity'=>1]);
        }
        $admin=User::where('role','admin')->orderBy('id')->firstOrFail();
        return [$buyer,$sellerUser,$seller,$order,$order->items()->latest('id')->first(),$admin];
    }

    private function payload($item, string $solution='refund'): array
    {
        return ['return_type'=>$solution,'problem_type'=>'Damaged Item','reason'=>'This selected product arrived broken.',
            'items'=>[['order_item_id'=>$item->id,'quantity'=>1]],
            'evidence'=>[UploadedFile::fake()->createWithContent('evidence.png',base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1cAAAAASUVORK5CYII='))]];
    }

    public function test_manual_receipt_required_and_admin_refund_only_is_atomic_and_idempotent(): void
    {
        [$buyer,$sellerUser,$seller,$order,$item,$admin]=$this->fixture();
        $this->actingAs($buyer)->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item))->assertUnprocessable();
        $this->assertDatabaseCount('order_returns',0);$this->assertDatabaseCount('complaints',0);
        $this->patchJson('/api/v1/buyer/orders/'.$order->id.'/confirm-received')->assertOk();
        $response=$this->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item))->assertCreated()
            ->assertJsonPath('data.status','pending')->assertJsonPath('data.refund_amount','180.00');
        $return=OrderReturn::findOrFail($response->json('data.id'));$case=$return->complaint;
        $this->assertSame('Selected Product',$return->return_items[0]['product_name']);
        Storage::disk('public')->assertExists($case->evidence[0]);
        $this->getJson('/api/v1/buyer/complaints')->assertOk()->assertJsonPath('data.0.productName','Selected Product')->assertJsonCount(1,'data.0.evidence')->assertJsonPath('data.0.productImage',Storage::disk('public')->url('products/selected.jpg'));
        $this->get('/buyer/view-order?order='.$order->order_number)->assertOk()->assertSee('Under Investigation')->assertSee('data-vo-action="return" disabled',false)->assertDontSee('Schedule Return Pickup');
        $this->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item))->assertUnprocessable();
        $this->actingAs($sellerUser)->patchJson('/api/v1/seller/returns-refunds/'.$return->id.'/decision',['decision'=>'accepted'])->assertForbidden();
        $this->get('/seller/performance/data')->assertOk()->assertJsonPath('complaints.0.productName','Selected Product')->assertJsonCount(1,'complaints.0.evidence')->assertJsonPath('complaints.0.party1Photo',Storage::disk('public')->url('profiles/buyer.jpg'))->assertJsonPath('complaints.0.party2Photo',Storage::disk('public')->url('profiles/seller.jpg'));
        $this->actingAs($admin)->getJson('/api/v1/admin/complaints')->assertOk()->assertJsonPath('data.0.productName','Selected Product')->assertJsonCount(1,'data.0.evidence');
        $this->patchJson('/api/v1/admin/complaints/'.$case->id.'/status',['status'=>'resolved'])->assertStatus(409);
        $url='/api/v1/admin/complaints/'.$case->id.'/return-decision';
        $this->patchJson($url,['action'=>'accept','notes'=>'Evidence and discussion confirm the damage.'])->assertOk()->assertJsonPath('data.status','refunded');
        $this->patchJson($url,['action'=>'accept'])->assertOk();
        $this->assertSame('resolved',$case->fresh()->status);
        $this->assertDatabaseHas('buyer_wallets',['user_id'=>$buyer->id,'balance'=>180]);
        $this->assertDatabaseHas('seller_wallet_transactions',['seller_id'=>$seller->id,'entry_type'=>'refund','source_id'=>$return->id,'amount'=>-180]);
        $this->assertSame(1,DB::table('buyer_wallet_transactions')->where('reference_type','order_return')->where('reference_id',$return->id)->count());
        $this->assertSame(1,DB::table('seller_wallet_transactions')->where('entry_type','refund')->where('source_id',$return->id)->count());
        $this->assertTrue(DB::table('buyer_notifications')->where('buyer_id',$buyer->id)->exists());$this->assertTrue($sellerUser->notifications()->exists());
        $this->assertSame('refunded',$return->fresh()->status);
    }

    public function test_admin_can_change_either_solution_and_return_requires_pickup_then_receipt(): void
    {
        [$buyer,$sellerUser,$seller,$order,$item,$admin]=$this->fixture();
        $this->actingAs($buyer)->patchJson('/api/v1/buyer/orders/'.$order->id.'/confirm-received')->assertOk();
        $response=$this->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item,'return'))->assertCreated();
        $return=OrderReturn::findOrFail($response->json('data.id'));$url='/api/v1/admin/complaints/'.$return->complaint_id.'/return-decision';
        $pickup=['pickup_date'=>now()->addDay()->toDateString(),'pickup_time'=>'10:00','pickup_address'=>'Confirmed pickup address'];
        $this->postJson('/api/v1/buyer/returns/'.$return->id.'/pickup',$pickup)->assertStatus(409);
        $this->actingAs($admin)->patchJson($url,['action'=>'change_solution'])->assertOk()->assertJsonPath('data.return_type','refund')->assertJsonPath('data.buyer_solution','return');
        $this->patchJson($url,['action'=>'change_solution'])->assertOk()->assertJsonPath('data.return_type','return');
        $this->patchJson($url,['action'=>'accept'])->assertOk()->assertJsonPath('data.status','accepted');
        $this->patchJson($url,['action'=>'received'])->assertStatus(409);
        $this->assertSame(0,DB::table('buyer_wallet_transactions')->where('type','refund_credit')->count());
        $this->actingAs($buyer)->get('/buyer/view-order?order='.$order->order_number)->assertOk()->assertSee('Schedule Return Pickup');
        $this->postJson('/api/v1/buyer/returns/'.$return->id.'/pickup',$pickup)->assertOk()->assertJsonPath('data.shipping_status','scheduled')->assertJsonPath('data.pickup_address',$order->delivery_address);
        $this->postJson('/api/v1/buyer/returns/'.$return->id.'/pickup',$pickup)->assertStatus(409);
        $this->actingAs($admin)->patchJson($url,['action'=>'received'])->assertOk()->assertJsonPath('data.status','refunded');
        $this->patchJson($url,['action'=>'received'])->assertOk();
        $this->assertNotNull($return->fresh()->returned_at);
        $this->assertDatabaseHas('buyer_wallets',['user_id'=>$buyer->id,'balance'=>180]);
        $this->assertSame(1,DB::table('buyer_wallet_transactions')->where('type','refund_credit')->count());
    }

    public function test_decline_records_decision_without_wallet_transfer_and_other_users_cannot_decide(): void
    {
        [$buyer,$sellerUser,$seller,$order,$item,$admin]=$this->fixture();
        $this->actingAs($buyer)->patchJson('/api/v1/buyer/orders/'.$order->id.'/confirm-received')->assertOk();
        $response=$this->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item))->assertCreated();
        $return=OrderReturn::findOrFail($response->json('data.id'));$url='/api/v1/admin/complaints/'.$return->complaint_id.'/return-decision';
        $this->patchJson($url,['action'=>'accept'])->assertForbidden();
        $limited=User::create(['name'=>'Limited','email'=>'limited@example.com','password'=>'Password123!','role'=>'admin']);
        $this->actingAs($limited)->patchJson($url,['action'=>'accept'])->assertForbidden();
        $this->actingAs($admin)->patchJson($url,['action'=>'decline','notes'=>'Evidence does not substantiate the issue.'])->assertOk()->assertJsonPath('data.status','declined');
        $this->assertSame(0,DB::table('buyer_wallet_transactions')->where('type','refund_credit')->count());
        $this->assertDatabaseHas('complaint_updates',['complaint_id'=>$return->complaint_id,'type'=>'return_declined']);
        $this->assertSame('resolved',$return->complaint->status);
    }

    public function test_cod_wallet_and_first_seller_wallet_sync_deduct_the_refund_only_once(): void
    {
        [$buyer,$sellerUser,$seller,$order,$item,$admin]=$this->fixture();
        $user=fn($role)=>User::create(['name'=>'COD '.$role,'email'=>uniqid().'@example.com','password'=>'Password123!','role'=>$role,'registration_status'=>'active']);
        $logisticsUser=$user('logistics');$hubUser=$user('delivery_hub');$riderUser=$user('rider');
        $person=['first_name'=>'COD','last_name'=>'Test','sex'=>'other','contact_no'=>'09170000001','birthday'=>'1990-01-01','age'=>36,
            'province'=>'Laguna','municipality'=>'Calamba','barangay'=>'Real','street'=>'Main','house_number'=>'1'];
        $logistics=\App\Models\Logistics\Logistics::create([...$person,'user_id'=>$logisticsUser->id,'business_name'=>'COD Logistics','registration_status'=>'active']);
        $branch=\App\Models\Logistics\LogisticsBranch::create(['logistics_id'=>$logistics->id,'name'=>'Calamba','address'=>'Calamba']);
        $hub=\App\Models\Logistics\LogisticsDeliveryHub::create(['logistics_branch_id'=>$branch->id,'user_id'=>$hubUser->id,'hub_name'=>'COD Hub',
            'province'=>'Laguna','municipality'=>'Calamba','barangay'=>'Real','postal_code'=>'4027','street'=>'Main','manager_name'=>'COD Manager',
            'manager_email'=>'cod@example.com','manager_phone'=>'09170000001','login_email'=>$hubUser->email,'status'=>'active']);
        $rider=\App\Models\Rider\Rider::create([...$person,'user_id'=>$riderUser->id,'logistics_branch_id'=>$branch->id,
            'vehicle'=>'Motorcycle','plate_number'=>'COD-123','registration_status'=>'active']);
        $this->actingAs($buyer)->patchJson('/api/v1/buyer/orders/'.$order->id.'/confirm-received')->assertOk();
        DB::table('rider_cod_collections')->insert(['rider_id'=>$rider->id,'order_id'=>$order->id,'amount'=>320,'collected_at'=>now()]);
        $remittance=DB::table('rider_remittances')->insertGetId(['rider_id'=>$rider->id,'amount'=>320,'method'=>'hub_turn_in','status'=>'approved']);
        DB::table('cod_settlements')->insert(['order_id'=>$order->id,'remittance_id'=>$remittance,'delivery_hub_id'=>$hub->id,'logistics_id'=>$logistics->id,
            'cod_amount'=>320,'shipping_fee'=>50,'commission_base'=>270,'commission_rate'=>10,'commission_amount'=>27,'seller_amount'=>243,
            'status'=>'released','distributed_at'=>now(),'seller_released_at'=>now()]);
        DB::table('cod_wallet_accounts')->insert(['owner_key'=>'seller:'.$seller->id.':available','owner_type'=>'seller','owner_id'=>$seller->id,'account_type'=>'available','balance'=>243]);
        $response=$this->postJson('/api/v1/buyer/orders/'.$order->id.'/returns',$this->payload($item))->assertCreated();
        $return=OrderReturn::findOrFail($response->json('data.id'));
        $this->actingAs($admin)->patchJson('/api/v1/admin/complaints/'.$return->complaint_id.'/return-decision',['action'=>'accept'])->assertOk();
        $this->assertDatabaseHas('cod_wallet_accounts',['owner_key'=>'seller:'.$seller->id.':available','balance'=>63]);
        $wallet=app(\App\Services\SellerWalletService::class);$wallet->sync($seller);$wallet->sync($seller);
        $this->assertEquals(63,$wallet->summary($seller)['balance']);
        $this->assertDatabaseHas('buyer_wallets',['user_id'=>$buyer->id,'balance'=>180]);
        $this->assertSame(1,DB::table('seller_wallet_transactions')->where('entry_type','refund')->where('source_id',$return->id)->count());
    }

    public function test_return_window_is_seven_days_from_manual_receipt(): void
    {
        [$buyer, , , $order, $item] = $this->fixture();
        $this->actingAs($buyer)->patchJson("/api/v1/buyer/orders/{$order->id}/confirm-received")
            ->assertOk()->assertJsonPath('order.return_request_deadline', $order->fresh()->buyer_received_at->addDays(7)->toISOString());
        $this->get('/buyer/view-order?order='.$order->order_number)->assertOk()->assertSee('Return / Refund deadline:');
        $this->travel(8)->days();
        $this->postJson("/api/v1/buyer/orders/{$order->id}/returns", $this->payload($item))
            ->assertUnprocessable()->assertJsonPath('message', 'The 7-day return and refund request window has expired.');
        $this->get('/buyer/view-order?order='.$order->order_number)
            ->assertOk()->assertSee('data-vo-action="return" disabled', false);
    }

    public function test_hub_schedules_approved_returns_and_the_third_failed_pickup_closes_the_case(): void
    {
        [$buyer, , , $order, $item, $admin] = $this->fixture();
        $user = fn ($role) => User::create([
            'name' => 'Pickup Test', 'email' => uniqid().'@example.test', 'password' => 'Password123!',
            'role' => $role, 'registration_status' => 'active',
        ]);
        $hubUser = $user('delivery_hub');
        $riderUser = $user('rider');
        $company = Logistics::create([
            'user_id' => $user('logistics')->id, 'last_name' => 'Carrier', 'first_name' => 'Test', 'sex' => 'other',
            'contact_no' => '09170000001', 'birthday' => '1990-01-01', 'age' => 36, 'province' => 'Laguna',
            'municipality' => 'Calamba', 'barangay' => 'Real', 'street' => 'Main', 'house_number' => '1',
            'business_name' => 'Test Logistics', 'registration_status' => 'active',
        ]);
        $branch = LogisticsBranch::create(['logistics_id' => $company->id, 'name' => 'Calamba', 'address' => 'Calamba']);
        $hub = LogisticsDeliveryHub::create([
            'logistics_branch_id' => $branch->id, 'user_id' => $hubUser->id, 'hub_name' => 'Calamba Hub',
            'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Real', 'postal_code' => '4027',
            'street' => 'Main', 'manager_name' => 'Hub Manager', 'manager_email' => $hubUser->email,
            'manager_phone' => '09170000002', 'login_email' => $hubUser->email, 'status' => 'active',
        ]);
        $rider = Rider::create([
            'user_id' => $riderUser->id, 'logistics_branch_id' => $branch->id, 'last_name' => 'Rider',
            'first_name' => 'Test', 'sex' => 'other', 'contact_no' => '09170000003', 'birthday' => '1990-01-01',
            'age' => 36, 'province' => 'Laguna', 'municipality' => 'Calamba', 'barangay' => 'Real',
            'street' => 'Main', 'house_number' => '1', 'vehicle' => 'Motorcycle', 'plate_number' => 'ABC123',
            'municipal_hub' => 'Calamba Hub', 'registration_status' => 'active', 'logistics_operation' => 'pickup',
        ]);
        DeliveryHubRiderRegistration::create(['logistics_delivery_hub_id' => $hub->id, 'user_id' => $riderUser->id, 'status' => 'approved']);
        $order->shipment()->update(['logistics_delivery_hub_id' => $hub->id]);
        $this->actingAs($buyer)->patchJson("/api/v1/buyer/orders/{$order->id}/confirm-received")->assertOk();
        $return = OrderReturn::findOrFail($this->postJson("/api/v1/buyer/orders/{$order->id}/returns", $this->payload($item, 'return'))
            ->assertCreated()->json('data.id'));
        $decision = "/api/v1/admin/complaints/{$return->complaint_id}/return-decision";
        $pickup = "/api/v1/delivery_hub/returns/{$return->id}/pickup";
        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/parcel-tracking')
            ->assertOk()->assertJsonPath('data.returns.0.can_schedule_pickup', false);
        $this->actingAs($admin)->patchJson($decision, ['action' => 'accept'])->assertOk();
        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/parcel-tracking')
            ->assertOk()->assertJsonPath('data.returns.0.can_schedule_pickup', true);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->actingAs($hubUser)->postJson($pickup, [
                'pickup_date' => now()->addDay()->toDateString(), 'pickup_time' => '09:00',
                'rider_id' => $rider->id, 'attempt' => $attempt,
            ])->assertOk()->assertJsonPath('data.shipping_status', 'scheduled');
            $this->actingAs($riderUser)->getJson('/api/v1/rider/pickup-tasks')->assertOk()
                ->assertJsonPath('return_pickups.0.attempt', $attempt);
            $this->travel(1)->days();
            $outcome = ['attempt' => $attempt, 'outcome' => 'failed', 'reason' => 'Buyer was unavailable.'];
            $this->patchJson("/api/v1/rider/return-pickups/{$return->id}", $outcome)->assertOk()
                ->assertJsonPath('data.failed_pickup_attempts', $attempt)
                ->assertJsonPath('data.status', $attempt < 3 ? 'accepted' : 'cancelled');
            $this->patchJson("/api/v1/rider/return-pickups/{$return->id}", $outcome)->assertConflict();
        }
        $this->assertSame('resolved', $return->complaint()->value('status'));
        $this->assertNull($return->fresh()->refunded_at);
        $this->actingAs($hubUser)->getJson('/api/v1/delivery_hub/parcel-tracking')
            ->assertOk()->assertJsonPath('data.returns.0.can_schedule_pickup', false);
    }

    public function test_seller_report_appears_in_all_three_complaint_views(): void
    {
        [$buyer, $sellerUser, , $order, , $admin] = $this->fixture();

        $response = $this->actingAs($sellerUser)->postJson('/api/v1/seller/order-complaints', [
            'order_id' => $order->id,
            'type' => 'Buyer Conduct',
            'description' => 'The seller provided details about the reported incident.',
            'evidence' => [UploadedFile::fake()->createWithContent('evidence.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1cAAAAASUVORK5CYII='))],
        ])->assertCreated();
        $reference = $response->json('reference');

        $this->get('/seller/performance/data')->assertOk()->assertSee($reference);
        $this->actingAs($admin)->getJson('/api/v1/admin/complaints')->assertOk()->assertSee($reference);
        $this->actingAs($buyer)->getJson('/api/v1/buyer/complaints')->assertOk()->assertSee($reference);
    }
}
