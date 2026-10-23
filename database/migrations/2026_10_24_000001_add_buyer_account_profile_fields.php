<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyers', function (Blueprint $table) {
            if (! Schema::hasColumn('buyers', 'zip_code')) {
                $table->string('zip_code', 20)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'building')) {
                $table->string('building', 150)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'subdivision')) {
                $table->string('subdivision', 150)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'cover_photo_path')) {
                $table->string('cover_photo_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buyers', function (Blueprint $table) {
            foreach (['zip_code', 'building', 'subdivision', 'cover_photo_path'] as $column) {
                if (Schema::hasColumn('buyers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
