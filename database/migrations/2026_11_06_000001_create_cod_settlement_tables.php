<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rider_remittances', function (Blueprint $table): void {
            $table->foreignId('delivery_hub_id')->nullable()->after('rider_id')
                ->constrained('logistics_delivery_hubs')->nullOnDelete();
            $table->decimal('actual_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('discrepancy_amount', 12, 2)->nullable()->after('actual_amount');
            $table->timestamp('cash_in_at')->nullable();
            $table->foreignId('cash_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['delivery_hub_id', 'status', 'created_at'], 'remittance_hub_status_idx');
        });

        Schema::create('rider_cod_remittance_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('remittance_id')->constrained('rider_remittances')->cascadeOnDelete();
            $table->foreignId('cod_collection_id')->constrained('rider_cod_collections')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['remittance_id', 'cod_collection_id'], 'cod_remit_collection_unique');
            $table->index('cod_collection_id', 'cod_remit_collection_idx');
        });

        Schema::create('hub_cod_cash_ins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_hub_id')->constrained('logistics_delivery_hubs')->restrictOnDelete();
            $table->foreignId('remittance_id')->unique()->constrained('rider_remittances')->restrictOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('reference', 80)->unique();
            $table->timestamp('received_at');
            $table->timestamps();
            $table->index(['delivery_hub_id', 'received_at']);
        });

        Schema::create('cod_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('remittance_id')->constrained('rider_remittances')->restrictOnDelete();
            $table->foreignId('delivery_hub_id')->constrained('logistics_delivery_hubs')->restrictOnDelete();
            $table->foreignId('logistics_id')->nullable()->constrained('logistics')->nullOnDelete();
            $table->decimal('cod_amount', 12, 2);
            $table->decimal('shipping_fee', 12, 2);
            $table->decimal('commission_base', 12, 2);
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('seller_amount', 12, 2);
            $table->string('status', 20)->default('held');
            $table->timestamp('distributed_at');
            $table->timestamp('seller_released_at')->nullable();
            $table->timestamps();
            $table->index(['delivery_hub_id', 'status', 'distributed_at']);
        });

        Schema::create('cod_wallet_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('owner_key', 100)->unique();
            $table->string('owner_type', 30);
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('account_type', 30);
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamps();
            $table->index(['owner_type', 'owner_id', 'account_type'], 'cod_wallet_owner_idx');
        });

        Schema::create('cod_wallet_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wallet_account_id')->constrained('cod_wallet_accounts')->restrictOnDelete();
            $table->string('entry_type', 40);
            $table->string('source_type', 40);
            $table->unsignedBigInteger('source_id');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('reference', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->unique(['wallet_account_id', 'entry_type', 'source_type', 'source_id'], 'cod_wallet_source_unique');
            $table->index(['wallet_account_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cod_wallet_transactions');
        Schema::dropIfExists('cod_wallet_accounts');
        Schema::dropIfExists('cod_settlements');
        Schema::dropIfExists('hub_cod_cash_ins');
        Schema::dropIfExists('rider_cod_remittance_items');
        Schema::table('rider_remittances', function (Blueprint $table): void {
            $table->dropIndex('remittance_hub_status_idx');
            $table->dropConstrainedForeignId('cash_in_by');
            $table->dropConstrainedForeignId('delivery_hub_id');
            $table->dropColumn(['actual_amount', 'discrepancy_amount', 'cash_in_at']);
        });
    }
};
