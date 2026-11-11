<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('delivery_hub_parcel_issues')) {
            Schema::create('delivery_hub_parcel_issues', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('delivery_hub_id')->constrained('logistics_delivery_hubs')->cascadeOnDelete();
                $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('tracking_number', 90);
                $table->string('issue_type', 100);
                $table->text('description');
                $table->string('evidence_name')->nullable();
                $table->string('evidence_path')->nullable();
                $table->string('status', 40)->default('Open');
                $table->json('accountability')->nullable();
                $table->timestamps();
            });
        }

        $indexes = Schema::getIndexes('delivery_hub_parcel_issues');
        $hasHubTrackingUnique = collect($indexes)->contains(fn (array $index): bool =>
            ($index['unique'] ?? false) && ($index['columns'] ?? []) === ['delivery_hub_id', 'tracking_number']
        );
        if (! $hasHubTrackingUnique) {
            Schema::table('delivery_hub_parcel_issues', function (Blueprint $table): void {
                $table->unique(['delivery_hub_id', 'tracking_number'], 'dh_parcel_issue_hub_track_uq');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_hub_parcel_issues');
    }
};
