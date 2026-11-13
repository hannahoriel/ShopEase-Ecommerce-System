<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rider_cod_collections', function (Blueprint $table): void {
            $table->dropForeign(['order_id']);
            $table->dropUnique(['order_id']);
            $table->foreignId('order_return_id')->nullable()->after('order_id')
                ->unique()->constrained('order_returns')->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->change();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rider_cod_collections', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('order_return_id');
            $table->dropForeign(['order_id']);
            $table->foreignId('order_id')->nullable(false)->change();
            $table->unique('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }
};
