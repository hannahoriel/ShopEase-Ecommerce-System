<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('buyer_received_at')->nullable()->after('status');
            $table->boolean('buyer_received_automatically')->default(false)->after('buyer_received_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['buyer_received_at', 'buyer_received_automatically']);
        });
    }
};
