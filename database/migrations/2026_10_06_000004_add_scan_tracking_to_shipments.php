<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->string('scan_token', 64)->nullable()->unique();
            $table->string('current_location')->nullable();
        });

        Schema::create('shipment_scans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('status');
            $table->string('location');
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['shipment_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_scans');

        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropUnique(['scan_token']);
            $table->dropColumn(['scan_token', 'current_location']);
        });
    }
};
