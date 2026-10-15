<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table): void {
            if (! Schema::hasColumn('sellers', 'zip_code')) {
                $table->string('zip_code', 10)->nullable();
            }

            if (! Schema::hasColumn('sellers', 'building')) {
                $table->string('building')->nullable();
            }

            if (! Schema::hasColumn('sellers', 'subdivision')) {
                $table->string('subdivision')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table): void {
            $columns = array_values(array_filter(
                ['zip_code', 'building', 'subdivision'],
                fn (string $column): bool => Schema::hasColumn('sellers', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
