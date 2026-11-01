<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('logistics_id')->nullable()->after('seller_id')->constrained('logistics')->nullOnDelete();
            $table->index(['logistics_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['logistics_id', 'status']);
            $table->dropConstrainedForeignId('logistics_id');
        });
    }
};
