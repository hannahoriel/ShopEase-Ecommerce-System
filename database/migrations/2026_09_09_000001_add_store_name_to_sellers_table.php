<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sellers', 'store_name')) {
            Schema::table('sellers', function (Blueprint $table): void {
                $table->string('store_name')->nullable()->after('house_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sellers', 'store_name')) {
            Schema::table('sellers', function (Blueprint $table): void {
                $table->dropColumn('store_name');
            });
        }
    }
};
