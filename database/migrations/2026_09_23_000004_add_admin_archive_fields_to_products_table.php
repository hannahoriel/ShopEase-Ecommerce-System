<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('archived_by_admin')->default(false)->after('is_archived');
            $table->string('archive_reason')->nullable()->after('archived_by_admin');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['archived_by_admin', 'archive_reason']);
        });
    }
};
