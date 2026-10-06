<?php

namespace Tests\Feature;

use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_submit_one_product_review_and_seller_can_read_it(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Review Shop',
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Canvas tote bag',
            'category' => 'Electronics & Gadgets',
            'price' => 500,
            'stock_quantity' => 8,
            'status' => 'active',
            'is_archived' => false,
        ]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/products/{$product->id}/reviews", [
                'rating' => 5,
                'title' => 'Great quality',
                'body' => 'The bag is sturdy and arrived quickly.',
            ])
            ->assertCreated()
            ->assertJsonPath('summary.average_rating', 5)
            ->assertJsonPath('summary.total_reviews', 1)
            ->assertJsonPath('review.buyer_name', $buyer->name);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'rating' => 5,
            'title' => 'Great quality',
        ]);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/feedback')
            ->assertOk()
            ->assertJsonPath('feedback.total', 1)
            ->assertJsonPath('feedback.data.0.customer', $buyer->name)
            ->assertJsonPath('feedback.data.0.product', 'Canvas tote bag')
            ->assertJsonPath('products.0.total_reviews', 1);

        $this->actingAs($sellerUser)
            ->getJson("/api/v1/seller/feedback/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('product.category', 'Electronics & Gadgets')
            ->assertJsonPath('summary.average_rating', 5)
            ->assertJsonPath('data.0.customer', $buyer->name);

        $this->actingAs($sellerUser)
            ->get('/seller/customer-feedback')
            ->assertOk()
            ->assertSee('sellerFeedbackConfig');

        $this->actingAs($buyer)
            ->get('/buyer/product?id=' . $product->id)
            ->assertOk()
            ->assertSee('productReviewForm');

        $this->actingAs($buyer)
            ->getJson("/api/v1/buyer/products/{$product->id}/reviews")
            ->assertOk()
            ->assertJsonPath('summary.total_reviews', 1)
            ->assertJsonPath('data.0.body', 'The bag is sturdy and arrived quickly.');
    }

    public function test_buyer_cannot_submit_a_second_review_for_the_same_product(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $sellerUser->id, 'store_name' => 'Review Shop']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Reusable bottle',
            'price' => 250,
            'status' => 'active',
            'is_archived' => false,
        ]);
        ProductReview::create([
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'rating' => 4,
            'body' => 'Works well.',
        ]);

        $this->actingAs($buyer)
            ->postJson("/api/v1/buyer/products/{$product->id}/reviews", [
                'rating' => 3,
                'body' => 'Trying to post a second review.',
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('product_reviews', 1);
    }

    public function test_seller_cannot_access_another_sellers_product_review_details(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_SELLER]);
        $ownerSeller = Seller::create(['user_id' => $owner->id, 'store_name' => 'Owner Shop']);
        $otherSellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        Seller::create(['user_id' => $otherSellerUser->id, 'store_name' => 'Other Shop']);
        $product = Product::create([
            'seller_id' => $ownerSeller->id,
            'name' => 'Private seller product',
            'price' => 100,
            'status' => 'active',
            'is_archived' => false,
        ]);

        $this->actingAs($otherSellerUser)
            ->getJson("/api/v1/seller/feedback/products/{$product->id}")
            ->assertNotFound();
    }

    public function test_customer_feedback_entry_totals_match_seller_reviews_and_active_filters(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create(['user_id' => $sellerUser->id, 'store_name' => 'Feedback Shop']);
        $otherSellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $otherSeller = Seller::create(['user_id' => $otherSellerUser->id, 'store_name' => 'Other Feedback Shop']);
        $products = collect(['First product', 'Second product'])->map(
            fn (string $name): Product => Product::create([
                'seller_id' => $seller->id,
                'name' => $name,
                'price' => 100,
                'status' => 'active',
                'is_archived' => false,
            ])
        );

        foreach (range(1, 9) as $index) {
            $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
            $product = $products[($index - 1) % $products->count()];

            ProductReview::create([
                'product_id' => $product->id,
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'rating' => $index <= 3 ? 5 : 4,
                'title' => $index === 1 ? 'Searchable feedback' : 'Customer feedback',
                'body' => "Review entry {$index}",
            ]);
        }

        $otherProduct = Product::create([
            'seller_id' => $otherSeller->id,
            'name' => 'Other seller product',
            'price' => 100,
            'status' => 'active',
            'is_archived' => false,
        ]);
        ProductReview::create([
            'product_id' => $otherProduct->id,
            'seller_id' => $otherSeller->id,
            'buyer_id' => User::factory()->create(['role' => User::ROLE_BUYER])->id,
            'rating' => 5,
            'title' => 'Other seller feedback',
            'body' => 'This review must not be included.',
        ]);

        $this->actingAs($sellerUser)
            ->getJson('/api/v1/seller/feedback?per_page=7&page=1')
            ->assertOk()
            ->assertJsonPath('feedback.total', 9)
            ->assertJsonPath('feedback.per_page', 7)
            ->assertJsonPath('feedback.current_page', 1)
            ->assertJsonCount(7, 'feedback.data');

        $this->getJson('/api/v1/seller/feedback?per_page=7&page=2')
            ->assertOk()
            ->assertJsonPath('feedback.total', 9)
            ->assertJsonPath('feedback.current_page', 2)
            ->assertJsonCount(2, 'feedback.data');

        $this->getJson('/api/v1/seller/feedback?rating=5')
            ->assertOk()
            ->assertJsonPath('feedback.total', 3)
            ->assertJsonCount(3, 'feedback.data');

        $this->getJson('/api/v1/seller/feedback?search=Searchable')
            ->assertOk()
            ->assertJsonPath('feedback.total', 1)
            ->assertJsonCount(1, 'feedback.data');
    }
}
