<?php

namespace Tests\Feature;

use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use Database\Seeders\ProductReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_repeatable_feedback_for_active_products_only(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Seeded Review Shop',
        ]);

        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Seeded product',
            'price' => 100,
            'status' => 'active',
            'is_archived' => false,
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Inactive product',
            'price' => 100,
            'status' => 'pending',
            'is_archived' => false,
        ]);

        $this->seed(ProductReviewSeeder::class);
        $this->seed(ProductReviewSeeder::class);

        $this->assertDatabaseCount('product_reviews', 5);
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'rating' => 5,
            'title' => 'Excellent quality',
        ]);
        $this->assertSame([3, 4, 4, 5, 5], ProductReview::query()
            ->where('product_id', $product->id)
            ->orderBy('rating')
            ->pluck('rating')
            ->all());
        $this->assertSame(5, User::query()->where('role', User::ROLE_BUYER)->count());

        $buyer = User::query()->where('email', 'althea.reyes@shopease.test')->firstOrFail();
        $this->actingAs($buyer)
            ->getJson("/api/v1/buyer/products/{$product->id}/reviews")
            ->assertOk()
            ->assertJsonPath('summary.average_rating', 4.2)
            ->assertJsonPath('summary.total_reviews', 5)
            ->assertJsonPath('summary.rating_counts.3', 1)
            ->assertJsonPath('summary.rating_counts.4', 2)
            ->assertJsonPath('summary.rating_counts.5', 2);

        $this->getJson("/api/v1/buyer/products/{$product->id}/reviews?rating=5")
            ->assertOk()
            ->assertJsonPath('summary.average_rating', 4.2)
            ->assertJsonPath('summary.total_reviews', 5)
            ->assertJsonPath('total', 2);
    }
}
