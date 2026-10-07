<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_conversations', function (Blueprint $table): void {
            $table->foreignId('product_id')
                ->nullable()
                ->after('order_id')
                ->constrained('products')
                ->nullOnDelete();
            $table->index(
                ['seller_id', 'type', 'buyer_id', 'product_id'],
                'seller_conversation_product_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('seller_conversations', function (Blueprint $table): void {
            $table->dropIndex('seller_conversation_product_unique');
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
