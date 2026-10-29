<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_delivery_hubs', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->unique()->after('logistics_branch_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_delivery_hubs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
