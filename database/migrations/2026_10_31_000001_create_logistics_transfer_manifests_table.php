<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_transfer_manifests', function (Blueprint $table): void {
            $table->id();
            $table->string('batch_key', 100)->unique('ltm_batch_key_unique');
            $table->string('origin_type', 30);
            $table->unsignedBigInteger('origin_id');
            $table->string('destination_type', 30);
            $table->unsignedBigInteger('destination_id');
            $table->unsignedBigInteger('courier_registration_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('courier_name', 150)->nullable();
            $table->string('vehicle_reference', 80)->nullable();
            $table->foreignId('returning_manifest_id')->nullable()->constrained('logistics_transfer_manifests')->nullOnDelete();
            $table->unique('returning_manifest_id', 'ltm_return_manifest_unique');
            $table->string('status', 20)->default('loaded');
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamps();
            $table->index(['destination_type', 'destination_id', 'status'], 'ltm_destination_status_idx');
            $table->index(['origin_type', 'origin_id', 'status'], 'ltm_origin_status_idx');
        });

        Schema::create('logistics_transfer_manifest_shipments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transfer_manifest_id')->constrained('logistics_transfer_manifests', 'id', 'ltms_manifest_fk')->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained('shipments', 'id', 'ltms_shipment_fk')->cascadeOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->unique(['transfer_manifest_id', 'shipment_id'], 'ltms_manifest_shipment_unique');
            $table->index(['shipment_id', 'received_at'], 'ltms_shipment_received_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_transfer_manifest_shipments');
        Schema::dropIfExists('logistics_transfer_manifests');
    }
};
