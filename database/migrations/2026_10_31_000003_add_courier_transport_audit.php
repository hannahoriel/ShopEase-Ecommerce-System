<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            $table->timestamp('arrival_confirmed_at')->nullable();
        });

        Schema::table('logistics_transfer_manifest_shipments', function (Blueprint $table): void {
            $table->string('load_status', 20)->default('pending');
            $table->timestamp('loaded_at')->nullable();
            $table->foreignId('loaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('discrepancy_note')->nullable();
            $table->index(['transfer_manifest_id', 'load_status'], 'ltms_manifest_load_status_idx');
        });

        DB::table('logistics_transfer_manifest_shipments')
            ->whereIn('transfer_manifest_id', DB::table('logistics_transfer_manifests')
                ->where('status', 'loaded')
                ->select('id'))
            ->update(['load_status' => 'loaded']);

        Schema::create('logistics_transfer_manifest_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transfer_manifest_id')
                ->constrained('logistics_transfer_manifests', 'id', 'ltme_manifest_fk')
                ->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 40);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['transfer_manifest_id', 'occurred_at'], 'ltme_manifest_occurred_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_transfer_manifest_events');

        Schema::table('logistics_transfer_manifest_shipments', function (Blueprint $table): void {
            $table->dropIndex('ltms_manifest_load_status_idx');
            $table->dropConstrainedForeignId('received_by');
            $table->dropConstrainedForeignId('loaded_by');
            $table->dropColumn(['load_status', 'loaded_at', 'discrepancy_note']);
        });

        Schema::table('logistics_transfer_manifests', function (Blueprint $table): void {
            $table->dropColumn(['scheduled_at', 'departed_at', 'arrival_confirmed_at']);
        });
    }
};
