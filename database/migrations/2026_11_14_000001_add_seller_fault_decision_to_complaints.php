<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table): void {
            $table->boolean('seller_at_fault')->nullable()->after('status');
            $table->text('seller_fault_notes')->nullable()->after('seller_at_fault');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table): void {
            $table->dropColumn(['seller_at_fault', 'seller_fault_notes']);
        });
    }
};
