<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_vouchers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('title', 65);
            $table->string('discount_type', 10);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_spend', 10, 2)->default(0);
            $table->decimal('maximum_discount', 10, 2);
            $table->json('product_ids')->nullable();
            $table->string('eligible_buyers', 20)->default('all');
            $table->string('stacking', 20)->default('none');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('claimed')->default(0);
            $table->unsignedInteger('per_buyer')->default(1);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->text('terms')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['seller_id', 'code']);
            $table->index(['enabled', 'starts_at', 'ends_at']);
        });

        Schema::create('seller_voucher_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_voucher_id')->constrained('seller_vouchers')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
            $table->index(['seller_voucher_id', 'buyer_id']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('shipping_fee', 10, 2)->default(50);
            $table->decimal('voucher_discount', 10, 2)->default(0);
            $table->string('voucher_code', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['shipping_fee', 'voucher_discount', 'voucher_code']);
        });
        Schema::dropIfExists('seller_voucher_redemptions');
        Schema::dropIfExists('seller_vouchers');
    }
};
