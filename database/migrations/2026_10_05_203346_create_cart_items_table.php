<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            // Stores selected variation/color/size name at time of add
            $table->string('variation', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('size', 100)->nullable();
            $table->timestamps();

            // Composite unique via shorter columns
            $table->unique(['user_id', 'product_id', 'variation', 'color', 'size'], 'cart_items_unique');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
