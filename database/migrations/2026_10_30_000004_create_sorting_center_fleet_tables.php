<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sorting_center_vehicles')) {
            Schema::create('sorting_center_vehicles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sorting_center_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('type', 80)->default('Truck');
            $table->string('plate_number', 50);
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('capacity', 80)->nullable();
            $table->string('driver_name', 150)->nullable();
            $table->dateTime('registration_date')->nullable();
            $table->string('availability', 40)->default('Available');
            $table->string('color', 60)->nullable();
            $table->string('or_cr_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
                $table->index(['sorting_center_id', 'plate_number'], 'sc_vehicles_center_plate_idx');
            });
        }

        if (! Schema::hasTable('sorting_center_fleet_archives')) {
            Schema::create('sorting_center_fleet_archives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sorting_center_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->string('record_type', 20);
            $table->unsignedBigInteger('record_id');
            $table->text('reason');
            $table->json('snapshot');
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at');
            $table->timestamps();
            $table->unique(['sorting_center_id', 'record_type', 'record_id'], 'sc_fleet_archive_record_unique');
                $table->index(['sorting_center_id', 'record_type', 'archived_at'], 'sc_fleet_archives_lookup_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sorting_center_fleet_archives');
        Schema::dropIfExists('sorting_center_vehicles');
    }
};
