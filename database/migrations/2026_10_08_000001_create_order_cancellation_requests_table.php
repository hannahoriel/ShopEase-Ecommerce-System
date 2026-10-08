<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_cancellation_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('buyer_reason');
            $table->string('buyer_other_reason', 500)->nullable();
            $table->boolean('auto_approved')->default(false);
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('seller_reason')->nullable();
            $table->string('seller_other_reason', 500)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['buyer_id', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_cancellation_requests');
    }
};
