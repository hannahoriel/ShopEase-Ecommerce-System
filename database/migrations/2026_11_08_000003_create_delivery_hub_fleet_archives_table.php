<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_hub_fleet_archives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_delivery_hub_id')->constrained('logistics_delivery_hubs')->cascadeOnDelete();
            $table->string('record_type', 20);
            $table->unsignedBigInteger('record_id');
            $table->text('reason');
            $table->json('snapshot');
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at');
            $table->timestamps();
            $table->unique(['logistics_delivery_hub_id', 'record_type', 'record_id'], 'dh_fleet_archive_record_unique');
            $table->index(['logistics_delivery_hub_id', 'record_type', 'archived_at'], 'dh_fleet_archives_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_hub_fleet_archives');
    }
};
