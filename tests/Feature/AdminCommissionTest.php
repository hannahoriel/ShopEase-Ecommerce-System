<?php

namespace Tests\Feature;

use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_commission_page_and_api_use_earned_database_transactions(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        [$seller, $order] = $this->createOrder('completed', 1000, 100);
        $this->createOrder('pending', 2000, 200);

        $this->assertDatabaseHas('commission_transactions', [
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'status' => 'earned',
            'commission_amount' => 100,
        ]);

        $this->actingAs($admin)
            ->get('/admin/commission')
            ->assertOk()
            ->assertSee('#'.$order->order_number)
            ->assertSee('₱ 100');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/commissions?per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.order_id', '#'.$order->order_number)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('stats.total_commission', 100)
            ->assertJsonPath('stats.total_orders', 1)
            ->assertJsonPath('stats.total_sellers_charged', 1);
    }

    public function test_commission_api_supports_search_and_date_filters(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Searchable Shop',
            'registration_status' => 'active',
        ]);
        $order = Order::create([
            'buyer_id' => User::factory()->create(['role' => User::ROLE_BUYER])->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-SEARCH-0001',
            'total' => 500,
            'commission_amount' => 50,
            'status' => 'completed',
        ]);
        CommissionTransaction::query()->where('order_id', $order->id)->update([
            'earned_at' => '2026-10-06 12:00:00',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/commissions?search=Searchable&from=2026-10-06&to=2026-10-06')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.seller', 'Searchable Shop');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/commissions?from=2026-10-07')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_platform_commission_rate_is_always_ten_percent(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        [$seller, $order] = $this->createOrder('completed', 1000, 50);

        $this->assertSame('100.00', $order->commission_amount);
        $this->assertDatabaseHas('commission_transactions', [
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'order_amount' => 1000,
            'commission_rate' => 10,
            'commission_amount' => 100,
        ]);

        $this->actingAs($admin)
            ->get('/admin/commission')
            ->assertOk()
            ->assertSee('10%');

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/commissions')
            ->assertOk()
            ->assertJsonPath('stats.rate', 10)
            ->assertJsonPath('stats.total_commission', 100);
    }

    public function test_commission_page_and_api_are_restricted_to_admins(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->get('/admin/commission')
            ->assertForbidden();

        $this->actingAs($buyer)
            ->getJson('/api/v1/admin/commissions')
            ->assertForbidden();
    }

    public function test_order_status_changes_update_the_commission_ledger(): void
    {
        [, $order] = $this->createOrder('pending', 1000, 100);
        $transaction = CommissionTransaction::where('order_id', $order->id)->firstOrFail();

        $this->assertSame('pending', $transaction->status);
        $this->assertNull($transaction->earned_at);

        $order->update(['status' => 'completed']);
        $transaction->refresh();

        $this->assertSame('earned', $transaction->status);
        $this->assertNotNull($transaction->earned_at);
        $this->assertSame('100.00', $transaction->commission_amount);

        $order->update(['status' => 'refunded']);

        $this->assertDatabaseHas('commission_transactions', [
            'order_id' => $order->id,
            'status' => 'reversed',
            'earned_at' => null,
        ]);
    }

    /**
     * @return array{0: Seller, 1: Order}
     */
    private function createOrder(string $status, float $total, float $commission): array
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Commission Shop',
            'registration_status' => 'active',
        ]);
        $order = Order::create([
            'buyer_id' => User::factory()->create(['role' => User::ROLE_BUYER])->id,
            'seller_id' => $seller->id,
            'order_number' => 'SE-COMMISSION-'.fake()->unique()->numerify('####'),
            'total' => $total,
            'commission_amount' => $commission,
            'status' => $status,
        ]);

        return [$seller, $order];
    }
}
