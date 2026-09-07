<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Account type
            $table->enum('user_type', ['seller', 'buyer']);

            // Personal information
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->date('birthdate');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->string('password')->nullable(); // hashed password captured at signup

            // Address
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('street');
            $table->string('house_no')->nullable();
            $table->string('zip_code', 10);

            // Business information (sellers only)
            $table->string('business_name')->nullable();
            $table->string('business_category')->nullable();
            $table->string('business_permit_path')->nullable();

            // Verification document (all applicants)
            $table->string('valid_id_path');

            // Review workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->string('rejection_details', 300)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Set once the registration is approved and a live account is created
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'user_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
