<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_hub_rider_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_delivery_hub_id')->constrained('logistics_delivery_hubs')->cascadeOnDelete();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->string('rejection_reason', 500)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['logistics_delivery_hub_id', 'status', 'created_at'], 'dh_rider_reg_hub_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_hub_rider_registrations');
    }
};
