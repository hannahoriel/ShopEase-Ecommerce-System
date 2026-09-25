<?php

namespace Tests\Feature;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seller_compliance_page_requires_admin_access(): void
    {
        $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($buyer)
            ->get(route('admin.seller.compliance'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.seller.compliance'))
            ->assertOk();
    }

    public function test_admin_seller_compliance_page_renders_real_seller_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);

        Seller::create([
            'user_id' => $sellerUser->id,
            'first_name' => 'Real',
            'last_name' => 'Seller',
            'store_name' => 'Real Store Data',
            'registration_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.seller.compliance'))
            ->assertOk()
            ->assertSee('Real Store Data')
            ->assertSee('initialSellerComplianceData');
    }

    public function test_admin_can_fetch_live_seller_compliance_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $buyerUser = User::factory()->create(['role' => User::ROLE_BUYER]);

        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'store_name' => 'AnaCart',
            'business_name' => 'AnaCart',
            'contact_no' => '09171234567',
            'upload_id' => 'registration-documents/ana-valid-id.jpg',
            'upload_business_permit' => 'registration-documents/ana-business-permit.pdf',
            'registration_status' => 'active',
        ]);

        $sampleProduct = $seller->products()->create([
            'name' => 'Sample Product',
            'category' => 'health-and-beauty',
            'status' => 'pending',
            'price' => 250,
            'stock_quantity' => 10,
            'photos' => ['products/sample-product.jpg'],
        ]);
        $sampleProduct->productSpecifications()->createMany([
            ['key' => 'brand', 'value' => 'Seller Brand'],
            ['key' => 'material', 'value' => 'Cotton'],
            ['key' => 'weight', 'value' => '250g'],
            ['key' => 'country_of_origin', 'value' => 'Philippines'],
        ]);
        $sampleProduct->options()->createMany([
            ['type' => 'size', 'name' => 'Medium', 'price' => 0, 'stock' => 5, 'price_type' => 'base', 'sort_order' => 0],
            ['type' => 'color', 'name' => 'Blue', 'price' => 0, 'stock' => 5, 'price_type' => 'base', 'sort_order' => 0],
        ]);

        $order = Order::create([
            'buyer_id' => $buyerUser->id,
            'seller_id' => $seller->id,
            'total' => 250,
            'commission_amount' => 25,
            'status' => 'completed',
        ]);

        Complaint::create([
            'user_id' => $admin->id,
            'order_id' => $order->id,
            'subject' => 'Late shipment',
            'description' => 'Delivery delay complaint',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/seller-compliance?per_page=10')
            ->assertOk()
            ->assertJsonPath('summary.total_sellers', 1)
            ->assertJsonPath('summary.under_review', 1)
            ->assertJsonPath('data.0.store_name', 'AnaCart')
            ->assertJsonPath('data.0.compliance', 'under-review')
            ->assertJsonPath('data.0.products.0.name', 'Sample Product')
            ->assertJsonPath('data.0.products.0.status', 'pending')
            ->assertJsonPath('data.0.products.0.sold_count', 0)
            ->assertJsonPath('data.0.products.0.image_url', 'http://localhost:8000/storage/products/sample-product.jpg')
            ->assertJsonPath('data.0.products.0.specifications.brand', 'Seller Brand')
            ->assertJsonPath('data.0.products.0.sizes.0.name', 'Medium')
            ->assertJsonPath('data.0.products.0.colors.0.name', 'Blue')
            ->assertJsonPath('data.0.valid_id_url', 'http://localhost:8000/storage/registration-documents/ana-valid-id.jpg')
            ->assertJsonPath('data.0.business_permit_url', 'http://localhost:8000/storage/registration-documents/ana-business-permit.pdf');

        $this->actingAs($admin)
            ->getJson("/api/v1/admin/seller-compliance/{$seller->id}")
            ->assertOk()
            ->assertJsonPath('data.products.0.name', 'Sample Product')
            ->assertJsonPath('data.products.0.status', 'pending');
    }

    public function test_admin_can_approve_a_pending_product(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Review Store',
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Pending product',
            'price' => 100,
            'stock_quantity' => 4,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/seller-compliance/products/{$product->id}/approve")
            ->assertOk()
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_issue_warning_to_a_pending_product(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Warning Store',
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Warning product',
            'price' => 150,
            'stock_quantity' => 8,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/seller-compliance/products/{$product->id}/warn", [
                'reason' => 'Misleading product information',
                'details' => 'Product image does not match the actual listing.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'warning')
            ->assertJsonPath('data.warning_reason', 'Misleading product information')
            ->assertJsonPath('data.warning_details', 'Product image does not match the actual listing.')
            ->assertJsonPath('reason', 'Misleading product information')
            ->assertJsonPath('details', 'Product image does not match the actual listing.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'warning',
            'warning_reason' => 'Misleading product information',
            'warning_details' => 'Product image does not match the actual listing.',
        ]);
    }

    public function test_seller_inventory_view_exposes_warning_status_for_issued_products(): void
    {
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Warning Inventory Store',
            'registration_status' => 'active',
        ]);

        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Warning inventory product',
            'price' => 299,
            'stock_quantity' => 5,
            'status' => 'warning',
            'warning_reason' => 'Misleading product information',
            'warning_details' => 'Product image does not match the actual listing.',
        ]);

        $this->actingAs($sellerUser)
            ->get(route('seller.inventory'))
            ->assertOk()
            ->assertSee('Issue Warning')
            ->assertSee('Misleading product information')
            ->assertSee('Product image does not match the actual listing.');
    }

    public function test_admin_can_remove_a_product_and_persist_reason_for_vendor_archive_view(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Removal Store',
            'registration_status' => 'active',
        ]);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Removal product',
            'price' => 200,
            'stock_quantity' => 5,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/seller-compliance/products/{$product->id}/remove", [
                'reason' => 'Prohibited product',
                'details' => 'The product violates platform policy.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'archived')
            ->assertJsonPath('data.archived_by_admin', true)
            ->assertJsonPath('data.archive_reason', 'Prohibited product');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'archived',
            'is_archived' => true,
            'archived_by_admin' => 1,
            'archive_reason' => 'Prohibited product',
        ]);
    }

    public function test_archived_products_are_hidden_from_seller_compliance(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sellerUser = User::factory()->create(['role' => User::ROLE_SELLER]);
        $seller = Seller::create([
            'user_id' => $sellerUser->id,
            'store_name' => 'Archived Store',
            'registration_status' => 'active',
        ]);
        Product::create([
            'seller_id' => $seller->id,
            'name' => 'Archived product',
            'price' => 100,
            'stock_quantity' => 4,
            'status' => 'active',
            'is_archived' => true,
        ]);

        $this->actingAs($admin)
            ->getJson("/api/v1/admin/seller-compliance/{$seller->id}")
            ->assertOk()
            ->assertJsonCount(0, 'data.products');
    }
}
