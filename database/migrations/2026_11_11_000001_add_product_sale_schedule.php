<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('sale_discount_type', 10)->nullable()->after('sale_percentage');
            $table->decimal('sale_discount_value', 10, 2)->nullable()->after('sale_discount_type');
            $table->dateTime('sale_starts_at')->nullable()->after('sale_discount_value');
            $table->dateTime('sale_ends_at')->nullable()->after('sale_starts_at');
        });
        DB::table('products')->whereNotNull('sale_percentage')->update([
            'sale_discount_type' => 'percentage',
            'sale_discount_value' => DB::raw('sale_percentage'),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['sale_discount_type', 'sale_discount_value', 'sale_starts_at', 'sale_ends_at']);
        });
    }
};
