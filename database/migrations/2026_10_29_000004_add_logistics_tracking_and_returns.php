<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->foreignId('logistics_branch_id')->nullable()->after('rider_id')
                ->constrained('logistics_branches')->nullOnDelete();
            $table->foreignId('logistics_delivery_hub_id')->nullable()->after('logistics_branch_id')
                ->constrained('logistics_delivery_hubs')->nullOnDelete();
            $table->index(['logistics_branch_id', 'logistics_delivery_hub_id']);
        });

        Schema::create('order_returns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('return_type', 30)->default('return');
            $table->string('status', 30)->default('pending');
            $table->string('shipping_status', 40)->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable()->unique();
            $table->text('reason')->nullable();
            $table->text('solution')->nullable();
            $table->text('evidence_description')->nullable();
            $table->json('return_items')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
        });

        Schema::create('order_return_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_return_id')->constrained('order_returns')->cascadeOnDelete();
            $table->string('status', 40);
            $table->string('location')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['order_return_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_events');
        Schema::dropIfExists('order_returns');

        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropIndex(['logistics_branch_id', 'logistics_delivery_hub_id']);
            $table->dropConstrainedForeignId('logistics_delivery_hub_id');
            $table->dropConstrainedForeignId('logistics_branch_id');
        });
    }
};
