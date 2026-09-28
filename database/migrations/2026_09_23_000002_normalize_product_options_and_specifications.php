<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('name', 120);
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->string('price_type', 20)->default('addon');
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'type']);
            $table->unique(['product_id', 'type', 'name']);
        });

        Schema::create('product_specifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('key', 120);
            $table->text('value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'key']);
        });

        DB::table('products')->orderBy('id')->each(function (object $product): void {
            foreach (['variations', 'colors', 'sizes'] as $type) {
                $items = json_decode($product->{$type} ?? '[]', true) ?: [];

                foreach ($items as $index => $item) {
                    if (!is_array($item) || trim((string) ($item['name'] ?? '')) === '') {
                        continue;
                    }

                    DB::table('product_options')->insert([
                        'product_id' => $product->id,
                        'type' => $type === 'variations' ? 'variation' : rtrim($type, 's'),
                        'name' => trim((string) $item['name']),
                        'price' => (float) ($item['price'] ?? 0),
                        'stock' => (int) ($item['stock'] ?? 0),
                        'price_type' => $item['price_type'] ?? $item['priceType'] ?? 'addon',
                        'photo' => $item['photo'] ?? $item['photoData'] ?? null,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $specifications = json_decode($product->specifications ?? '{}', true) ?: [];
            foreach ($specifications as $index => $value) {
                $key = preg_replace('/^category_specifications\[|\]$/', '', (string) $index);

                DB::table('product_specifications')->insert([
                    'product_id' => $product->id,
                    'key' => $key,
                    'value' => is_scalar($value) ? (string) $value : json_encode($value),
                    'sort_order' => is_int($index) ? $index : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['variations', 'colors', 'sizes', 'specifications']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('variations')->nullable();
            $table->json('colors')->nullable();
            $table->json('sizes')->nullable();
            $table->json('specifications')->nullable();
        });

        DB::table('product_options')->orderBy('id')->each(function (object $option): void {
            $column = $option->type === 'variation' ? 'variations' : $option->type . 's';
            $items = json_decode(DB::table('products')->where('id', $option->product_id)->value($column) ?: '[]', true) ?: [];
            $items[] = [
                'name' => $option->name,
                'price' => $option->price,
                'stock' => $option->stock,
                'price_type' => $option->price_type,
                'photo' => $option->photo,
            ];
            DB::table('products')->where('id', $option->product_id)->update([$column => json_encode($items)]);
        });

        DB::table('product_specifications')->orderBy('id')->each(function (object $specification): void {
            $product = DB::table('products')->where('id', $specification->product_id)->first();
            $values = json_decode($product->specifications ?: '{}', true) ?: [];
            $values[$specification->key] = $specification->value;
            DB::table('products')->where('id', $specification->product_id)->update([
                'specifications' => json_encode($values),
            ]);
        });

        Schema::dropIfExists('product_specifications');
        Schema::dropIfExists('product_options');
    }
};
