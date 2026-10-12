<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'archive_details')) {
                $table->string('archive_details', 300)->nullable();
            }
            if (! Schema::hasColumn('products', 'archive_actor')) {
                $table->string('archive_actor', 20)->nullable();
            }
            if (! Schema::hasColumn('products', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        foreach (['archive_details', 'archive_actor', 'archived_at'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                Schema::table('products', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
