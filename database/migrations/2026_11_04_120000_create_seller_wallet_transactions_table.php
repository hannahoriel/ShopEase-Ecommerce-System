<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seller_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('entry_type', 40);
            $table->string('source_type', 35);
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 14, 2); // signed; positive credit, negative debit
            $table->string('reference', 100)->nullable();
            $table->string('description')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->unique(['seller_id', 'entry_type', 'source_type', 'source_id'], 'seller_wallet_unique_event');
            $table->index(['seller_id', 'occurred_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('seller_wallet_transactions'); }
};
