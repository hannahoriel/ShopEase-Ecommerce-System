<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_account_profiles', function (Blueprint $table): void {
            $table->string('middle_name', 80)->nullable();
            $table->json('permissions')->nullable();
            $table->json('message_permissions')->nullable();
            $table->boolean('is_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('admin_account_profiles', function (Blueprint $table): void {
            $table->dropColumn([
                'middle_name',
                'permissions',
                'message_permissions',
                'is_enabled',
            ]);
        });
    }
};
