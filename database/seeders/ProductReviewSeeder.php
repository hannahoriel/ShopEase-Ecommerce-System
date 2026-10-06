<?php

namespace Database\Seeders;

use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    private const REVIEWERS = [
        ['name' => 'Althea Reyes', 'email' => 'althea.reyes@shopease.test'],
        ['name' => 'Miguel Santos', 'email' => 'miguel.santos@shopease.test'],
        ['name' => 'Jamie Cruz', 'email' => 'jamie.cruz@shopease.test'],
        ['name' => 'Sofia Garcia', 'email' => 'sofia.garcia@shopease.test'],
        ['name' => 'Noah Mendoza', 'email' => 'noah.mendoza@shopease.test'],
    ];

    private const REVIEWS = [
        [
            'rating' => 5,
            'title' => 'Excellent quality',
            'body' => 'The quality exceeded my expectations. It arrived in great condition and I would happily order again.',
        ],
        [
            'rating' => 5,
            'title' => 'Worth the price',
            'body' => 'Exactly as described and a great value. The seller packed it carefully.',
        ],
        [
            'rating' => 4,
            'title' => 'Good purchase',
            'body' => 'The product works well and looks good. Delivery took a little longer than expected.',
        ],
        [
            'rating' => 4,
            'title' => 'Happy with my order',
            'body' => 'Good overall quality and the item matched the listing. I am happy with this purchase.',
        ],
        [
            'rating' => 3,
            'title' => 'It is okay',
            'body' => 'The product is usable and matches the description, though I expected slightly better finishing.',
        ],
    ];

    public function run(): void
    {
        $products = Product::query()
            ->where('status', 'active')
            ->where('is_archived', false)
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->command?->info('No active products found. Add products before seeding customer feedback.');

            return;
        }

        $buyers = collect(self::REVIEWERS)->map(
            fn (array $reviewer): User => User::firstOrCreate(
                ['email' => $reviewer['email']],
                [
                    'name' => $reviewer['name'],
                    'role' => User::ROLE_BUYER,
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            )
        );

        foreach ($products as $product) {
            foreach ($buyers as $index => $buyer) {
                $review = self::REVIEWS[$index];
                $createdAt = now()->subDays(($index * 2) + ($product->id % 10));

                ProductReview::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'buyer_id' => $buyer->id,
                    ],
                    [
                        'seller_id' => $product->seller_id,
                        ...$review,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ],
                );
            }
        }

        $this->command?->info('Customer feedback seeded for ' . $products->count() . ' active product(s).');
    }
}
