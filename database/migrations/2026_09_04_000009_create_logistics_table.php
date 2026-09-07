<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial', 10)->nullable();
            $table->string('sex', 20);
            $table->string('contact_no', 20);
            $table->date('birthday');
            $table->unsignedInteger('age');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('street');
            $table->string('house_number');
            $table->string('business_name')->nullable();
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
        Schema::dropIfExists('logistics');
    }
};
