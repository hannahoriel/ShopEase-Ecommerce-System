<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('type', 20);
            $table->foreignId('buyer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('complaint_id')->nullable()->unique()->constrained('complaints')->cascadeOnDelete();
            $table->string('seed_key')->nullable()->unique();
            $table->timestamps();

            $table->unique(['seller_id', 'type', 'buyer_id', 'order_id'], 'seller_conversation_buyer_order_unique');
            $table->index(['seller_id', 'type', 'updated_at']);
        });

        Schema::create('seller_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('seller_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->string('seed_key')->nullable()->unique();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['conversation_id', 'sender_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_messages');
        Schema::dropIfExists('seller_conversations');
    }
};
