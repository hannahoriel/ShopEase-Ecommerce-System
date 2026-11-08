<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hub_cash_in_otps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_hub_id')->constrained('logistics_delivery_hubs')->cascadeOnDelete();
            $table->foreignId('remittance_id')->constrained('rider_remittances')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('code_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();
            $table->index(['delivery_hub_id', 'remittance_id', 'requested_by', 'created_at'], 'hub_cash_otp_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_cash_in_otps');
    }
};
