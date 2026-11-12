<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            $table->unsignedTinyInteger('failed_pickup_attempts')->default(0);
            $table->foreignId('pickup_rider_id')->nullable()->constrained('riders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('pickup_rider_id');
            $table->dropColumn('failed_pickup_attempts');
        });
    }
};
