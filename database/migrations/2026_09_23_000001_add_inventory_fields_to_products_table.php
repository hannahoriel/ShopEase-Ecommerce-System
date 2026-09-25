<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->text('description')->nullable()->after('name');
            $table->string('category')->nullable()->after('description');
            $table->string('pricing_mode')->default('fixed')->after('price');
            $table->string('pricing_source')->nullable()->after('pricing_mode');
            $table->json('variations')->nullable()->after('pricing_source');
            $table->json('colors')->nullable()->after('variations');
            $table->json('sizes')->nullable()->after('colors');
            $table->json('specifications')->nullable()->after('sizes');
            $table->json('photos')->nullable()->after('specifications');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'description',
                'category',
                'pricing_mode',
                'pricing_source',
                'variations',
                'colors',
                'sizes',
                'specifications',
                'photos',
            ]);
        });
    }
};
