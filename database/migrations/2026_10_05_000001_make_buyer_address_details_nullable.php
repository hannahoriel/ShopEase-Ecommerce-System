<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyers', function (Blueprint $table) {
            $table->string('street')->nullable()->change();
            $table->string('house_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('buyers')
            ->whereNull('street')
            ->update(['street' => '']);

        DB::table('buyers')
            ->whereNull('house_number')
            ->update(['house_number' => '']);

        Schema::table('buyers', function (Blueprint $table) {
            $table->string('street')->nullable(false)->change();
            $table->string('house_number')->nullable(false)->change();
        });
    }
};
