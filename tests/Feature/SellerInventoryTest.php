<?php

namespace Tests\Feature;

use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            'category' => 'electronics',
            'photos' => ['https://example.test/keyboard.jpg'],
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

    public function test_new_inventory_products_default_to_pending_for_compliance_review(): void
    {
        [$sellerUser, $seller] = $this->createSeller('pending-review@example.com');

        $response = $this->actingAs($sellerUser)->postJson('/api/v1/seller/inventory', [
            'name' => 'Pending review product',
            'sku' => 'PENDING-1',
            'price' => 599.00,
            'stock_quantity' => 9,
            'category' => 'electronics',
            'photos' => ['https://example.test/pending-product.jpg'],
        ]);

        $response->assertCreated()->assertJsonPath('status', 'pending');
        $this->assertDatabaseHas('products', [
            'seller_id' => $seller->id,
            'sku' => 'PENDING-1',
            'status' => 'pending',
        ]);
    }

    public function test_seller_form_payload_with_title_and_stock_is_accepted(): void
    {
        [$sellerUser, $seller] = $this->createSeller('form-payload@example.com');

        $response = $this->actingAs($sellerUser)->post('/seller/inventory/products', [
            'title' => 'Product from form payload',
            'sku' => 'FORM-1',
            'description' => 'A valid product added from the form payload.',
            'price' => 199.50,
            'stock' => 8,
            'category' => 'electronics',
            'photos' => ['https://example.test/form-product.jpg'],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Product from form payload');

        $this->assertDatabaseHas('products', [
            'seller_id' => $seller->id,
            'sku' => 'FORM-1',
            'name' => 'Product from form payload',
            'stock_quantity' => 8,
        ]);
    }

    public function test_seller_edit_form_persists_product_options_variants_specifications_and_photos(): void
    {
        [$sellerUser, $seller] = $this->createSeller('inventory-edit@example.com');
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Before edit',
            'sku' => 'EDIT-1',
            'description' => 'Original description',
            'price' => 10,
            'stock_quantity' => 2,
            'category' => 'electronics',
            'photos' => ['https://example.test/old.jpg'],
            'status' => 'active',
        ]);

        $response = $this->actingAs($sellerUser)->post("/seller/inventory/products/{$product->id}", [
            '_method' => 'PATCH',
            'title' => 'After edit',
            'sku' => 'EDIT-1',
            'description' => 'Updated description',
            'status' => 'pending',
            'pricing_mode' => 'varies',
            'pricing_source' => 'variations',
            'category' => 'electronics-and-gadgets',
            'photos' => ['data:image/jpeg;base64,ZmFrZQ=='],
            'variation_items' => [
                ['name' => 'Large', 'price' => '25.00', 'price_type' => 'base', 'stock' => '4'],
            ],
            'color_items' => [
                ['name' => 'Blue', 'price' => '0', 'price_type' => 'addon', 'stock' => '4'],
            ],
            'category_specifications' => [
                'brand' => 'ShopEase',
            ],
            'variant_combinations' => json_encode([[
                'variations' => 'Large',
                'colors' => 'Blue',
                'pricing_mode' => 'varies',
                'pricing_source' => 'variations',
                'base_price' => 25,
                'additions' => ['colors' => 2],
                'additional_price' => 2,
                'final_price' => 27,
                'stock' => 4,
                'available' => true,
            ]]),
        ]);

        $response->assertOk()
            ->assertJsonPath('name', 'After edit')
            ->assertJsonPath('description', 'Updated description')
            ->assertJsonPath('price', '27.00')
            ->assertJsonPath('stock_quantity', 4)
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('photos.0', 'data:image/jpeg;base64,ZmFrZQ==')
            ->assertJsonPath('variations.0.name', 'Large')
            ->assertJsonPath('colors.0.name', 'Blue')
            ->assertJsonPath('specifications.brand', 'ShopEase')
            ->assertJsonPath('connected_variants.0.final_price', 27);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'After edit',
            'status' => 'pending',
            'stock_quantity' => 4,
        ]);
        $this->assertDatabaseHas('product_options', [
            'product_id' => $product->id,
            'type' => 'variation',
            'name' => 'Large',
            'price' => 25,
            'stock' => 4,
            'price_type' => 'base',
        ]);
        $this->assertDatabaseHas('product_variant_combinations', [
            'product_id' => $product->id,
            'final_price' => 27,
            'stock' => 4,
        ]);

        $this->actingAs($sellerUser)
            ->patchJson("/seller/inventory/products/{$product->id}", [
                'name' => 'Details editor update',
                'sku' => 'EDIT-1',
                'description' => 'Saved from Product Details.',
                'price' => 30,
                'stock_quantity' => 5,
                'category' => 'electronics-and-gadgets',
                'pricing_mode' => 'varies',
                'pricing_source' => 'variations',
                'status' => 'pending',
                'photos' => ['data:image/jpeg;base64,bmV3'],
                'category_specifications' => ['brand' => 'ShopEase Updated'],
                'variation_items' => [
                    ['name' => 'Large', 'price' => 30, 'price_type' => 'base', 'stock' => 5],
                ],
                'color_items' => [
                    ['name' => 'Blue', 'price' => 0, 'price_type' => 'addon', 'stock' => 5],
                ],
                'variant_combinations' => [[
                    'variations' => 'Large',
                    'colors' => 'Blue',
                    'pricing_mode' => 'varies',
                    'pricing_source' => 'variations',
                    'base_price' => 30,
                    'additions' => ['colors' => 3],
                    'additional_price' => 3,
                    'final_price' => 33,
                    'stock' => 5,
                    'available' => true,
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('name', 'Details editor update')
            ->assertJsonPath('price', '33.00')
            ->assertJsonPath('stock_quantity', 5)
            ->assertJsonPath('specifications.brand', 'ShopEase Updated')
            ->assertJsonPath('connected_variants.0.final_price', 33);
    }

    public function test_variable_price_product_uses_and_persists_buyer_option_price_and_stock(): void
    {
        [$sellerUser, $seller] = $this->createSeller('variable-price@example.com');

        $response = $this->actingAs($sellerUser)->post('/seller/inventory/products', [
            'title' => 'Variable price product',
            'sku' => 'VARIABLE-1',
            'pricing_mode' => 'varies',
            'pricing_source' => 'variations',
            'category' => 'electronics',
            'photos' => ['https://example.test/variable-product.jpg'],
            'variation_items' => [
                [
                    'name' => 'Small',
                    'price' => 125.50,
                    'price_type' => 'base',
                    'stock' => 4,
                ],
                [
                    'name' => 'Large',
                    'price' => 175.00,
                    'price_type' => 'base',
                    'stock' => 3,
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('price', '125.50')
            ->assertJsonPath('stock_quantity', 7)
            ->assertJsonPath('variations.0.name', 'Small')
            ->assertJsonPath('variations.1.stock', 3);

        $this->assertDatabaseHas('products', [
            'seller_id' => $seller->id,
            'sku' => 'VARIABLE-1',
            'pricing_mode' => 'varies',
            'pricing_source' => 'variations',
            'price' => 125.50,
            'stock_quantity' => 7,
        ]);
    }

    public function test_create_form_payload_persists_photos_options_specifications_and_derived_inventory(): void
    {
        [$sellerUser, $seller] = $this->createSeller('full-create-payload@example.com');
        Storage::fake('public');
        $photos = [
            'data:image/jpeg;base64,Zmlyc3Q=',
            'data:image/png;base64,c2Vjb25k',
        ];

        $response = $this->actingAs($sellerUser)->post('/seller/inventory/products', [
            'title' => 'Wireless Keyboard',
            'sku' => 'WIRELESS-KEYBOARD-1',
            'description' => 'A wireless keyboard with multiple choices.',
            'category' => 'electronics-and-gadgets',
            'pricing_mode' => 'varies',
            'pricing_source' => 'colors',
            'color_items' => [
                ['name' => 'Black', 'price' => '49.50', 'price_type' => 'base', 'stock' => '2'],
                ['name' => 'White', 'price' => '59.00', 'price_type' => 'base', 'stock' => '3'],
            ],
            'variation_items' => [
                [
                    'name' => 'Classic',
                    'price' => '5.00',
                    'price_type' => 'addon',
                    'stock' => '1',
                    'photo' => UploadedFile::fake()->image('classic.png'),
                ],
            ],
            'size_items' => [
                ['name' => 'Compact', 'price' => '0.00', 'price_type' => 'addon', 'stock' => '7'],
            ],
            'category_specifications' => [
                'brand' => 'ShopEase',
                'keyboard-layout' => 'US',
                'empty-field' => '',
            ],
            'photos' => $photos,
            'product_photos' => [
                UploadedFile::fake()->image('cover.jpg'),
                UploadedFile::fake()->image('back.png'),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('name', 'Wireless Keyboard')
            ->assertJsonPath('description', 'A wireless keyboard with multiple choices.')
            ->assertJsonPath('category', 'electronics-and-gadgets')
            ->assertJsonPath('pricing_mode', 'varies')
            ->assertJsonPath('pricing_source', 'colors')
            ->assertJsonPath('price', '49.50')
            ->assertJsonPath('stock_quantity', 5)
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('photos', $photos)
            ->assertJsonPath('colors.0.price_type', 'base')
            ->assertJsonPath('colors.1.price', 59)
            ->assertJsonPath('variations.0.price_type', 'addon')
            ->assertJsonPath('variations.0.stock', 1)
            ->assertJsonPath('sizes.0.name', 'Compact')
            ->assertJsonPath('specifications.brand', 'ShopEase')
            ->assertJsonPath('specifications.keyboard-layout', 'US')
            ->assertJsonMissingPath('specifications.empty-field');

        $product = Product::query()->where('sku', 'WIRELESS-KEYBOARD-1')->firstOrFail();

        $this->assertSame($seller->id, $product->seller_id);
        $this->assertSame($photos, $product->fresh()->photos);
        $this->assertDatabaseHas('product_specifications', [
            'product_id' => $product->id,
            'key' => 'brand',
            'value' => 'ShopEase',
        ]);
        $this->assertDatabaseHas('product_options', [
            'product_id' => $product->id,
            'type' => 'color',
            'name' => 'White',
            'price' => 59,
            'stock' => 3,
            'price_type' => 'base',
        ]);

        $optionPhoto = $product->options()->where('type', 'variation')->value('photo');
        $this->assertNotEmpty($optionPhoto);
        Storage::disk('public')->assertExists($optionPhoto);
    }

    public function test_fixed_price_is_applied_to_each_buyer_option_and_uploaded_product_photos_are_stored(): void
    {
        [$sellerUser] = $this->createSeller('fixed-create-payload@example.com');
        Storage::fake('public');

        $response = $this->actingAs($sellerUser)->post('/seller/inventory/products', [
            'title' => 'Fixed price product',
            'price' => '125.00',
            'stock' => '9',
            'category' => 'electronics',
            'variation_items' => [
                ['name' => 'Small', 'price' => '999.00', 'stock' => '4'],
                ['name' => 'Large', 'price' => '999.00', 'stock' => '5'],
            ],
            'product_photos' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('back.png'),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('pricing_mode', 'fixed')
            ->assertJsonPath('price', '125.00')
            ->assertJsonPath('stock_quantity', 9)
            ->assertJsonPath('variations.0.price', 125)
            ->assertJsonPath('variations.1.price', 125)
            ->assertJsonCount(2, 'photos');

        foreach ($response->json('photos') as $photoUrl) {
            $path = str_replace('/storage/', '', parse_url($photoUrl, PHP_URL_PATH));
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_variable_pricing_requires_a_valid_source_and_at_least_one_source_option(): void
    {
        [$sellerUser] = $this->createSeller('invalid-variable-payload@example.com');

        $this->actingAs($sellerUser)->postJson('/seller/inventory/products', [
            'title' => 'Missing source',
            'pricing_mode' => 'varies',
            'variation_items' => [
                ['name' => 'Small', 'price' => 10, 'stock' => 1],
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['pricing_source']);

        $this->actingAs($sellerUser)->postJson('/seller/inventory/products', [
            'title' => 'Wrong source options',
            'pricing_mode' => 'varies',
            'pricing_source' => 'variations',
            'color_items' => [
                ['name' => 'Black', 'price' => 10, 'stock' => 1],
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['variation_items']);

        $this->actingAs($sellerUser)->postJson('/seller/inventory/products', [
            'title' => 'Invalid source',
            'pricing_mode' => 'varies',
            'pricing_source' => 'unknown',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['pricing_source']);
    }

    public function test_product_creation_requires_a_category_and_at_least_one_photo_and_starts_pending(): void
    {
        [$sellerUser] = $this->createSeller('required-create-fields@example.com');

        $this->actingAs($sellerUser)->postJson('/api/v1/seller/inventory', [
            'name' => 'Incomplete product',
            'price' => 10,
            'stock_quantity' => 1,
            'status' => 'active',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['category', 'photos', 'product_photos']);

        $response = $this->actingAs($sellerUser)->postJson('/api/v1/seller/inventory', [
            'name' => 'Pending product',
            'price' => 10,
            'stock_quantity' => 1,
            'category' => 'electronics',
            'photos' => ['https://example.test/pending.jpg'],
            'status' => 'active',
        ]);

        $response->assertCreated()->assertJsonPath('status', 'pending');
    }

    public function test_connected_choice_variant_saves_exact_choices_final_price_and_stock(): void
    {
        [$sellerUser, $seller] = $this->createSeller('connected-choice-variant@example.com');

        $response = $this->actingAs($sellerUser)->post('/seller/inventory/products', [
            'title' => 'Connected choice product',
            'category' => 'electronics-and-gadgets',
            'photos' => ['data:image/jpeg;base64,ZmFrZQ=='],
            'pricing_mode' => 'varies',
            'pricing_source' => 'variations',
            'variation_items' => [
                ['name' => 'axa', 'price' => 0, 'stock' => 20],
                ['name' => 'bxa', 'price' => 125, 'stock' => 10],
            ],
            'color_items' => [
                ['name' => 'ASCSA', 'price' => 0, 'stock' => 20],
            ],
            'size_items' => [
                ['name' => 'CSAC', 'price' => 0, 'stock' => 20],
            ],
            'variant_combinations' => json_encode([[
                'variations' => 'axa',
                'colors' => 'ASCSA',
                'sizes' => 'CSAC',
                'pricing_mode' => 'varies',
                'pricing_source' => 'variations',
                'base_price' => 200,
                'additions' => ['colors' => 10, 'sizes' => 10],
                'additional_price' => 20,
                'final_price' => 220,
                'stock' => 7,
                'available' => true,
            ]]),
        ]);

        $response->assertCreated()
            ->assertJsonPath('price', '220.00')
            ->assertJsonPath('stock_quantity', 7)
            ->assertJsonPath('connected_variants.0.variations', 'axa')
            ->assertJsonPath('connected_variants.0.colors', 'ASCSA')
            ->assertJsonPath('connected_variants.0.sizes', 'CSAC')
            ->assertJsonPath('connected_variants.0.base_price', 200)
            ->assertJsonPath('connected_variants.0.additional_price', 20)
            ->assertJsonPath('connected_variants.0.final_price', 220)
            ->assertJsonPath('connected_variants.0.stock', 7)
            ->assertJsonPath('connected_variants.0.additions.colors', 10)
            ->assertJsonPath('connected_variants.0.additions.sizes', 10);

        $this->assertDatabaseHas('products', [
            'id' => $response->json('id'),
            'seller_id' => $seller->id,
            'price' => 220,
            'stock_quantity' => 7,
        ]);
        $this->assertDatabaseHas('product_variant_combinations', [
            'product_id' => $response->json('id'),
            'final_price' => 220,
            'stock' => 7,
        ]);
        $this->assertDatabaseHas('product_options', [
            'product_id' => $response->json('id'),
            'type' => 'variation',
            'name' => 'axa',
            'price' => 200,
            'price_type' => 'base',
        ]);
    }

    public function test_fixed_connected_variant_uses_fixed_price_and_rejects_unavailable_choices(): void
    {
        [$sellerUser] = $this->createSeller('fixed-connected-variant@example.com');
        $payload = [
            'title' => 'Fixed connected choice product',
            'category' => 'electronics-and-gadgets',
            'photos' => ['data:image/jpeg;base64,ZmFrZQ=='],
            'pricing_mode' => 'fixed',
            'price' => 0,
            'stock' => 50,
            'variation_items' => [
                ['name' => 'axa', 'price' => 0, 'stock' => 10],
            ],
            'color_items' => [
                ['name' => 'ASCSA', 'price' => 0, 'stock' => 10],
            ],
            'size_items' => [
                ['name' => 'CSAC', 'price' => 0, 'stock' => 10],
            ],
            'variant_combinations' => [[
                'variations' => 'axa',
                'colors' => 'ASCSA',
                'sizes' => 'CSAC',
                'pricing_mode' => 'fixed',
                'pricing_source' => null,
                'base_price' => 0,
                'additions' => [],
                'additional_price' => 0,
                'final_price' => 0,
                'stock' => 0,
                'available' => true,
            ]],
        ];

        $response = $this->actingAs($sellerUser)->postJson('/seller/inventory/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('price', '0.00')
            ->assertJsonPath('stock_quantity', 0)
            ->assertJsonPath('connected_variants.0.final_price', 0)
            ->assertJsonPath('connected_variants.0.stock', 0);

        $payload['variant_combinations'][0]['colors'] = 'not-a-saved-color';
        $this->actingAs($sellerUser)
            ->postJson('/seller/inventory/products', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['variant_combinations.0.colors']);
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

    public function test_seller_product_photos_are_persisted_for_admin_and_archived_views(): void
    {
        [$sellerUser, $seller] = $this->createSeller('photo-persist@example.com');
        $photos = [
            'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUVFRUYHSAgGBolGxUVITEhJSkrLi4uFx8zODMsNygtLisBCgoKDg0OGhAQGy0mICUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAOEA4QMBIgACEQEDEQH/xAAbAAADAQEBAQEAAAAAAAAAAAAABQYDBAcCA//EADgQAAIBAwIEAwUGBwUBAAAAAAABAgMEAQUSITEkQVFhBhMiMnGBkaGx0fAUQmKS8CNCUnLx4RUjQ2Lx/8QAGgEBAAMBAQEAAAAAAAAAAAAAAAABAgMEBQH/xAA1EQACAgICAgEEAQAAAAAAAAAAAQIRAwQSMQUSQVEyYXGBkaGx0fAUIrH/2Q==',
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAF',
        ];

        $response = $this->actingAs($sellerUser)->postJson('/api/v1/seller/inventory', [
            'name' => 'Photo product',
            'sku' => 'PHOTO-1',
            'description' => 'A product with persisted photos.',
            'price' => 499.00,
            'stock_quantity' => 3,
            'category' => 'electronics',
            'photos' => $photos,
            'status' => 'pending',
        ]);

        $response->assertCreated()
            ->assertJsonPath('photos.0', $photos[0])
            ->assertJsonPath('photos.1', $photos[1]);

        $product = Product::query()->where('sku', 'PHOTO-1')->firstOrFail();

        $this->assertSame($photos, $product->photos);
        $this->assertSame($photos, $product->fresh()->photos);
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

    public function test_seller_cannot_unarchive_a_product_removed_by_admin(): void
    {
        [$sellerUser, $seller] = $this->createSeller('admin-removed@example.com');
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Admin removed product',
            'price' => 259,
            'stock_quantity' => 3,
            'status' => 'archived',
            'is_archived' => true,
            'archived_by_admin' => true,
            'archive_reason' => 'Unsafe listing',
        ]);

        $this->actingAs($sellerUser)
            ->patchJson("/api/v1/seller/inventory/{$product->id}/unarchive")
            ->assertStatus(403)
            ->assertJsonPath('message', 'This product was removed by an administrator and cannot be restored by the seller.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_archived' => true,
            'archived_by_admin' => 1,
        ]);
    }

    public function test_archived_items_keep_the_original_product_name(): void
    {
        [$sellerUser, $seller] = $this->createSeller('archive-name@example.com');
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Original product title',
            'price' => 120,
            'stock_quantity' => 4,
            'status' => 'active',
        ]);

        $this->actingAs($sellerUser)
            ->get(route('seller.inventory'))
            ->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Original product title',
        ]);
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
