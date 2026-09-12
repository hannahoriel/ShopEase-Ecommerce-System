<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_initial', 10)->nullable();
            $table->string('sex', 20)->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('province')->nullable();
            $table->string('municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('business_name')->nullable();
            $table->string('line_of_business')->nullable();
            $table->string('upload_id')->nullable();
            $table->string('upload_business_permit')->nullable();
            $table->string('registration_status', 20)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
