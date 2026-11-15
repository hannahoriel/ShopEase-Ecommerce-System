<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            $table->string('vehicle_type', 80)->nullable()->after('vehicle_reference');
            $table->foreignId('assigned_by')->nullable()->after('vehicle_type')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_by');
            $table->dropColumn(['vehicle_type', 'assigned_at']);
        });
    }
};
