<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_combinations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->json('choices');
            $table->string('pricing_mode', 20);
            $table->string('pricing_source', 20)->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->json('additions');
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('available')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_combinations');
    }
};
