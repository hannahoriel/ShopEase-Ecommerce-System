<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            if (! Schema::hasColumn('logistics_transfer_manifests', 'order_return_id')) {
                $table->foreignId('order_return_id')
                    ->nullable()
                    ->unique()
                    ->constrained('order_returns')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            if (Schema::hasColumn('logistics_transfer_manifests', 'order_return_id')) {
                $table->dropConstrainedForeignId('order_return_id');
            }
        });
    }
};
