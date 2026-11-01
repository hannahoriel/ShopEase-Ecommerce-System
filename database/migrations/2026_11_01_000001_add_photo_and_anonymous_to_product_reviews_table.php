<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table): void {
            $table->string('photo_path')->nullable()->after('body');
            $table->boolean('is_anonymous')->default(false)->after('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table): void {
            $table->dropColumn(['photo_path', 'is_anonymous']);
        });
    }
};
