<?php

namespace Tests\Feature;

use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerVoucher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminVouchersTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create(['name' => 'Voucher Test', 'email' => uniqid().'@example.com', 'password' => 'Password123!', 'role' => $role]);
    }

    private function payload(array $extra = []): array
    {
        return [...[
            'title' => 'ShopEase Savings', 'code' => 'PLATFORM10', 'discountType' => 'percent',
            'discountValue' => 10, 'minimumSpend' => 100, 'maximumDiscount' => 100,
            'eligibleItems' => 'all', 'eligibleBuyers' => 'all', 'stacking' => 'none',
            'quantity' => 5, 'perBuyer' => 1, 'starts' => now()->addMinute()->toDateTimeString(),
            'ends' => now()->addMonth()->toDateTimeString(), 'enabled' => true,
        ], ...$extra];
    }

    private function shop(): array
    {
        $user = $this->user(User::ROLE_SELLER);
        $seller = Seller::create(['user_id' => $user->id, 'store_name' => 'Voucher Shop', 'registration_status' => 'active']);
        $product = Product::create(['seller_id' => $seller->id, 'name' => 'Test Product', 'price' => 1000,
            'sku' => uniqid('SKU'), 'stock_quantity' => 10, 'status' => 'active', 'is_archived' => false]);
        return [$user, $seller, $product];
    }

    public function test_admin_can_manage_platform_vouchers_but_not_seller_records(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload())
            ->assertCreated()->assertJsonPath('data.id', 'PLATFORM10')
            ->assertJsonPath('data.eligibleItems', 'All eligible ShopEase products');
        $voucher = SellerVoucher::whereNull('seller_id')->firstOrFail();
        $this->assertSame($admin->id, $voucher->created_by);
        $this->assertSame('PLATFORM10', $voucher->platform_code);
        $this->getJson('/api/v1/admin/vouchers')->assertOk()->assertJsonCount(1, 'data');
        $this->get('/admin/vouchers')->assertOk()->assertSee('Create a Platform Voucher')->assertSee('/admin/vouchers');
        $this->patchJson('/api/v1/admin/vouchers/'.$voucher->id, ['enabled' => false])->assertOk()->assertJsonPath('data.enabled', false);
        [$seller] = $this->shop();
        $this->actingAs($seller)->patchJson('/api/v1/seller/vouchers/'.$voucher->id, ['enabled' => true])->assertNotFound();
        $this->postJson('/api/v1/admin/vouchers', $this->payload(['code' => 'DENIED']))->assertForbidden();
        $this->getJson('/api/v1/seller/vouchers')->assertOk()->assertJsonCount(0, 'data')->assertJsonCount(1, 'platform');
        $this->postJson('/api/v1/seller/vouchers', $this->payload())->assertUnprocessable();
        $this->postJson('/api/v1/seller/vouchers', $this->payload(['code' => 'SHOP10']))->assertCreated();
        $shopVoucher = SellerVoucher::where('code', 'SHOP10')->firstOrFail();
        $this->actingAs($admin)->patchJson('/api/v1/admin/vouchers/'.$shopVoucher->id, ['enabled' => false])->assertNotFound();
        $this->getJson('/api/v1/admin/vouchers')->assertJsonCount(1, 'data');
    }

    public function test_platform_product_selection_accepts_multiple_shops_and_rejects_invalid_rules(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        [,,$first] = $this->shop(); [,,$second] = $this->shop();
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
            'eligibleItems' => 'selected', 'productIds' => $first->id.','.$second->sku,
        ]))->assertCreated()->assertJsonPath('data.productIds', [(string)$first->id,(string)$second->id]);
        foreach ([['productIds' => 'DOES-NOT-EXIST','eligibleItems' => 'selected'], ['discountValue' => 101], ['perBuyer' => 6], ['ends' => now()->subDay()->toDateTimeString()]] as $invalid) {
            $this->postJson('/api/v1/admin/vouchers', $this->payload(['code' => 'NEWCODE', ...$invalid]))->assertUnprocessable();
        }
        $this->postJson('/api/v1/admin/vouchers', $this->payload())->assertUnprocessable();
    }

    public function test_secondary_admin_needs_voucher_permission_to_write(): void
    {
        $this->user(User::ROLE_ADMIN);
        $limited = $this->user(User::ROLE_ADMIN);
        $this->actingAs($limited)->postJson('/api/v1/admin/vouchers', $this->payload())->assertForbidden();
        DB::table('admin_account_profiles')->insert(['user_id' => $limited->id, 'permissions' => json_encode(['Vouchers']), 'is_enabled' => true]);
        $this->postJson('/api/v1/admin/vouchers', $this->payload())->assertCreated();
    }

    public function test_buyer_can_preview_platform_discount_on_qualifying_shop_and_pause_is_enforced(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        [,,$first] = $this->shop(); [, $secondShop,$second] = $this->shop();
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
            'eligibleItems' => 'selected', 'productIds' => (string)$second->id,
        ]))->assertCreated();
        $voucher = SellerVoucher::whereNull('seller_id')->firstOrFail();
        $voucher->update(['starts_at' => now()->subMinute()]);
        $buyer = $this->user(User::ROLE_BUYER);
        CartItem::create(['user_id' => $buyer->id,'product_id' => $first->id,'quantity' => 1]);
        CartItem::create(['user_id' => $buyer->id,'product_id' => $second->id,'quantity' => 1]);
        $this->actingAs($buyer)->postJson('/api/v1/buyer/vouchers/preview',['code' => 'PLATFORM10'])
            ->assertOk()->assertJsonPath('data.discount', 100)->assertJsonPath('data.seller_id', $secondShop->id);
        $this->getJson('/api/v1/buyer/vouchers')->assertOk()->assertJsonPath('data.0.seller_name', 'ShopEase Admin');
        $this->actingAs($admin)->patchJson('/api/v1/admin/vouchers/'.$voucher->id,['enabled' => false])->assertOk();
        $this->actingAs($buyer)->postJson('/api/v1/buyer/vouchers/preview',['code' => 'PLATFORM10'])->assertUnprocessable();
        $voucher->update(['enabled' => true,'claimed' => $voucher->quantity]);
        $this->postJson('/api/v1/buyer/vouchers/preview',['code' => 'PLATFORM10'])->assertUnprocessable();
    }

    public function test_platform_voucher_is_redeemed_once_in_a_multi_shop_checkout(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        [, $firstShop, $first] = $this->shop(); [, $secondShop, $second] = $this->shop();
        foreach ([$firstShop,$secondShop] as $shop) $shop->update(['province' => 'Laguna', 'municipality' => 'Calamba']);
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
            'eligibleItems' => 'selected', 'productIds' => (string)$second->id,
        ]))->assertCreated();
        $voucher = SellerVoucher::whereNull('seller_id')->firstOrFail();
        $voucher->update(['starts_at' => now()->subMinute()]);
        $logisticsUser = $this->user(User::ROLE_LOGISTICS);
        $logisticsUser->update(['registration_status' => 'active']);
        $logistics = \App\Models\Logistics\Logistics::create([
            'user_id' => $logisticsUser->id, 'first_name' => 'Test', 'last_name' => 'Carrier', 'sex' => 'other',
            'contact_no' => '09170000001','birthday' => '1990-01-01','age' => 36,'province' => 'Laguna',
            'municipality' => 'Calamba','barangay' => 'Real','street' => 'Main','house_number' => '1',
            'business_name' => 'Test Logistics','registration_status' => 'active',
        ]);
        \App\Models\Logistics\LogisticsBranch::create(['logistics_id' => $logistics->id,'name' => 'Calamba',
            'address' => 'Calamba','province' => 'Laguna','municipality' => 'Calamba','status' => 'active']);
        $buyer = $this->user(User::ROLE_BUYER);
        foreach ([$first,$second] as $product) CartItem::create(['user_id' => $buyer->id,'product_id' => $product->id,'quantity' => 1]);
        $this->actingAs($buyer)->postJson('/api/v1/buyer/orders',[
            'delivery_name' => 'Buyer','delivery_phone' => '09170000002','delivery_address' => '1 Main Street',
            'delivery_province' => 'Laguna','delivery_municipality' => 'Calamba','payment_method' => 'Cash on Delivery',
            'voucher_code' => 'PLATFORM10','voucher_seller_id' => $secondShop->id,
            'logistics_selections' => [
                ['seller_id' => $firstShop->id,'logistics_id' => $logistics->id],
                ['seller_id' => $secondShop->id,'logistics_id' => $logistics->id],
            ],
        ])->assertCreated();
        $this->assertDatabaseHas('orders',['buyer_id' => $buyer->id,'seller_id' => $firstShop->id,'voucher_discount' => 0]);
        $this->assertDatabaseHas('orders',['buyer_id' => $buyer->id,'seller_id' => $secondShop->id,'voucher_discount' => 100,'voucher_code' => 'PLATFORM10']);
        $this->assertSame(1,$voucher->fresh()->claimed);
        $this->assertDatabaseCount('seller_voucher_redemptions',1);
        CartItem::create(['user_id' => $buyer->id,'product_id' => $second->id,'quantity' => 1]);
        $this->postJson('/api/v1/buyer/vouchers/preview',['code' => 'PLATFORM10'])->assertUnprocessable();
    }

    public function test_admin_category_voucher_discounts_only_products_in_that_category(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        [, $shop, $matching] = $this->shop();
        $matching->update(['category' => 'Electronics & Gadgets']);
        $other = Product::create(['seller_id' => $shop->id, 'name' => 'Other', 'price' => 500,
            'sku' => 'OTHER', 'stock_quantity' => 10, 'status' => 'active', 'category' => 'Pet Supplies']);
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
            'eligibleItems' => 'electronics-and-gadgets', 'maximumDiscount' => 1000,
        ]))->assertCreated()->assertJsonPath('data.eligibleItems', 'Electronics & Gadgets');
        $voucher = SellerVoucher::whereNull('seller_id')->firstOrFail();
        $this->assertSame('electronics-and-gadgets', $voucher->eligible_category);
        $this->assertNull($voucher->product_ids);
        $voucher->update(['starts_at' => now()->subMinute()]);
        $buyer = $this->user(User::ROLE_BUYER);
        $matchingCart = CartItem::create(['user_id' => $buyer->id, 'product_id' => $matching->id, 'quantity' => 1]);
        $otherCart = CartItem::create(['user_id' => $buyer->id, 'product_id' => $other->id, 'quantity' => 1]);
        $this->actingAs($buyer)->postJson('/api/v1/buyer/vouchers/preview', ['code' => 'PLATFORM10'])
            ->assertOk()->assertJsonPath('data.discount', 100);
        $matchingCart->delete();
        $this->postJson('/api/v1/buyer/vouchers/preview', ['code' => 'PLATFORM10'])->assertUnprocessable();
        $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
            'code' => 'BADCAT', 'eligibleItems' => 'invalid-category',
        ]))->assertUnprocessable();
        $this->get('/admin/vouchers')->assertOk()->assertSee('value="electronics-and-gadgets"', false)->assertDontSee('Eligible Product IDs or SKUs');
    }

    public function test_notification_failure_does_not_leave_a_partially_created_voucher(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        $buyer = $this->user(User::ROLE_BUYER);
        $preferences = $this->mock(\App\Services\BuyerNotificationPreferences::class);
        $preferences->shouldReceive('allows')->once()->andThrow(new \RuntimeException('Notification unavailable'));
        $this->withoutExceptionHandling();
        try {
            $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', $this->payload([
                'starts' => now()->startOfMinute()->toDateTimeString(),
            ]));
            $this->fail('Expected a notification failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Notification unavailable', $exception->getMessage());
        }
        $this->assertDatabaseMissing('seller_vouchers', ['code' => 'PLATFORM10']);
    }
}
