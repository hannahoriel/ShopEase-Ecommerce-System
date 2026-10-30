<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sorting_center_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('sex', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->string('email')->index();
            $table->string('phone', 30);
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('house_no')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('vehicle', 80)->default('Truck');
            $table->string('plate_number', 50);
            $table->string('or_cr_path')->nullable();
            $table->string('drivers_license_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['sorting_center_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_registrations');
    }
};
