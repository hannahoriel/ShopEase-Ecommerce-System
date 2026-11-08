<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hub_wallet_pins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_hub_id')->unique()->constrained('logistics_delivery_hubs')->cascadeOnDelete();
            $table->string('pin_hash', 255);
            $table->foreignId('set_by')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('verification_token_hash', 64)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('token_consumed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_wallet_pins');
    }
};
