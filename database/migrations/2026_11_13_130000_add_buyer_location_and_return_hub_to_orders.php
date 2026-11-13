<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'delivery_province')) {
                $table->string('delivery_province', 150)->nullable()->after('delivery_barangay');
            }
            if (! Schema::hasColumn('orders', 'delivery_municipality')) {
                $table->string('delivery_municipality', 150)->nullable()->after('delivery_province');
            }
        });

        Schema::table('order_returns', function (Blueprint $table): void {
            if (! Schema::hasColumn('order_returns', 'return_delivery_hub_id')) {
                $table->foreignId('return_delivery_hub_id')->nullable()
                    ->after('pickup_rider_id')->constrained('logistics_delivery_hubs')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            if (Schema::hasColumn('order_returns', 'return_delivery_hub_id')) {
                $table->dropConstrainedForeignId('return_delivery_hub_id');
            }
        });

        Schema::table('orders', function (Blueprint $table): void {
            $columns = array_filter(['delivery_province', 'delivery_municipality'], fn ($column) => Schema::hasColumn('orders', $column));
            if ($columns) $table->dropColumn($columns);
        });
    }
};
