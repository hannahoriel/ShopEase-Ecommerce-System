<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rider_cod_remittance_items', function (Blueprint $table): void {
            $table->unique('cod_collection_id', 'cod_collection_single_remittance_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rider_cod_remittance_items', function (Blueprint $table): void {
            $table->dropUnique('cod_collection_single_remittance_unique');
        });
    }
};
