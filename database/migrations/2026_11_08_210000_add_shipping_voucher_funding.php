<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_vouchers', function (Blueprint $table): void {
            $table->string('voucher_type', 20)->default('merchandise');
            $table->string('funding_source', 20)->default('seller');
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('voucher_type', 20)->nullable();
            $table->string('voucher_funding_source', 20)->nullable();
        });
        DB::table('seller_vouchers')->whereNull('seller_id')->update(['funding_source' => 'platform']);
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['voucher_type', 'voucher_funding_source']));
        Schema::table('seller_vouchers', fn (Blueprint $table) => $table->dropColumn(['voucher_type', 'funding_source']));
    }
};
