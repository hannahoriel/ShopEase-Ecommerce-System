<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cod_settlements', function (Blueprint $table): void {
            $table->decimal('platform_shipping_margin', 12, 2)->default(0)->after('shipping_fee');
        });
    }

    public function down(): void
    {
        Schema::table('cod_settlements', function (Blueprint $table): void {
            $table->dropColumn('platform_shipping_margin');
        });
    }
};
