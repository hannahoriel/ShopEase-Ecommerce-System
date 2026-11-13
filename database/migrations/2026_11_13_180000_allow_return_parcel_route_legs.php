<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            if (Schema::hasColumn('logistics_transfer_manifests', 'order_return_id')) {
                // MySQL requires an index for the foreign key. Add a regular
                // index first so removing the one-return unique key keeps FK valid.
                $table->index('order_return_id', 'ltm_order_return_idx');
                $table->dropUnique(['order_return_id']);
            }
        });

        Schema::table('order_returns', function (Blueprint $table): void {
            if (! Schema::hasColumn('order_returns', 'delivery_rider_id')) {
                $table->foreignId('delivery_rider_id')->nullable()->constrained('riders')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            if (Schema::hasColumn('order_returns', 'delivery_rider_id')) {
                $table->dropConstrainedForeignId('delivery_rider_id');
            }
        });

        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            if (Schema::hasColumn('logistics_transfer_manifests', 'order_return_id')) {
                $table->unique('order_return_id');
                $table->dropIndex('ltm_order_return_idx');
            }
        });
    }
};
