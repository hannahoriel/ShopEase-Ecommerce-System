<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_branches', function (Blueprint $table): void {
            $table->string('province', 120)->nullable();
            $table->string('municipality', 120)->nullable();
            $table->string('barangay', 120)->nullable();
            $table->string('postal_code', 4)->nullable();
            $table->string('house_no', 80)->nullable();
            $table->string('street', 150)->nullable();
            $table->string('building', 150)->nullable();
            $table->string('subdivision', 150)->nullable();
            $table->string('manager_email')->nullable();
            $table->string('login_email')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('archived_at')->nullable();

            $table->index(['logistics_id', 'archived_at']);
        });

        Schema::create('logistics_delivery_hubs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_branch_id')
                ->constrained('logistics_branches')
                ->cascadeOnDelete();
            $table->string('hub_name', 120);
            $table->string('province', 120);
            $table->string('municipality', 120);
            $table->string('barangay', 120);
            $table->string('postal_code', 4);
            $table->string('house_no', 80)->nullable();
            $table->string('street', 150);
            $table->string('building', 150)->nullable();
            $table->string('subdivision', 150)->nullable();
            $table->string('manager_name', 120);
            $table->string('manager_email');
            $table->string('manager_phone', 20);
            $table->string('login_email');
            $table->string('status', 20)->default('active');
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index(['logistics_branch_id', 'archived_at']);
            $table->index(['logistics_branch_id', 'hub_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_delivery_hubs');

        Schema::table('logistics_branches', function (Blueprint $table): void {
            $table->dropIndex(['logistics_id', 'archived_at']);
            $table->dropColumn([
                'province',
                'municipality',
                'barangay',
                'postal_code',
                'house_no',
                'street',
                'building',
                'subdivision',
                'manager_email',
                'login_email',
                'status',
                'archived_at',
            ]);
        });
    }
};
