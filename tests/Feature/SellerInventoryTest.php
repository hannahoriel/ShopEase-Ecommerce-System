<?php

namespace Tests\Feature;

use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_manage_only_their_inventory(): void
    {
        [$sellerUser, $seller] = $this->createSeller('seller@example.com');
        [, $otherSeller] = $this->createSeller('other@example.com');
        $otherProduct = Product::create([
            'seller_id' => $otherSeller->id,
            'name' => 'Other product',
            'sku' => 'OTHER-1',
            'price' => 25,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($sellerUser)->postJson('/api/v1/seller/inventory', [
            'name' => 'Wireless keyboard',
            'sku' => 'KEYBOARD-1',
            'price' => 1499.50,
            'stock_quantity' => 12,
            'status' => 'active',
        ]);

        $response->assertCreated()->assertJsonPath('name', 'Wireless keyboard');
        $productId = $response->json('id');

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/inventory?search=keyboard')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $productId);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/inventory/{$productId}", ['stock_quantity' => 8])
            ->assertOk()
            ->assertJsonPath('stock_quantity', 8);

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/inventory/{$otherProduct->id}")
            ->assertNotFound();

        $this->actingAs($sellerUser)
            ->deleteJson("/api/v1/seller/inventory/{$productId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $productId]);
        $this->assertDatabaseHas('products', ['id' => $otherProduct->id]);
    }

    public function test_inventory_validates_duplicate_sku_for_the_same_seller(): void
    {
        [$sellerUser, $seller] = $this->createSeller('seller@example.com');
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Existing product',
            'sku' => 'DUPLICATE-1',
            'price' => 10,
            'stock_quantity' => 1,
        ]);

        $this->actingAs($sellerUser)
            ->postJson('/api/v1/seller/inventory', [
                'name' => 'Another product',
                'sku' => 'DUPLICATE-1',
                'price' => 20,
                'stock_quantity' => 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sku']);
    }

    public function test_non_sellers_cannot_access_inventory(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

        $this->actingAs($buyer)
            ->getJson('/api/v1/seller/inventory')
            ->assertForbidden();
    }

    public function test_seller_can_archive_and_unarchive_a_product(): void
    {
        [$sellerUser, $seller] = $this->createSeller('archive@example.com');
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Archive me',
            'price' => 100,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/inventory/{$product->id}/archive")
            ->assertOk()
            ->assertJsonPath('is_archived', true);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/inventory')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/inventory?archived=1')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $product->id);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/inventory/{$product->id}/unarchive")
            ->assertOk()
            ->assertJsonPath('is_archived', false);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/inventory')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }

    private function createSeller(string $email): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'role' => User::ROLE_SELLER,
        ]);
        $seller = Seller::create([
            'user_id' => $user->id,
            'store_name' => 'Test store',
            'registration_status' => 'active',
        ]);

        return [$user, $seller];
    }
}
