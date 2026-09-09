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
        Schema::create('seller_registrations', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Registration Session
            |--------------------------------------------------------------------------
            |
            | Used to identify one seller registration draft while
            | the user moves between Step 1, Step 2, and Step 3.
            |
            */

            $table->uuid('session_token')->unique();


            /*
            |--------------------------------------------------------------------------
            | Seller Personal Information
            |--------------------------------------------------------------------------
            */

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();

            $table->string('sex');

            $table->string('email');

            $table->string('contact_no');

            $table->date('birthday');

            $table->unsignedInteger('age')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Seller Address
            |--------------------------------------------------------------------------
            */

            $table->string('province');

            $table->string('municipality');

            $table->string('barangay');

            $table->string('zip_code', 10);

            $table->string('street')->nullable();

            $table->string('house_no')->nullable();

            $table->string('building')->nullable();

            $table->string('subdivision')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Seller Documents
            |--------------------------------------------------------------------------
            |
            | We store the file path/name instead of the actual file
            | inside MySQL.
            |
            */

            $table->string('valid_id')->nullable();

            $table->string('business_permit')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Business Information
            |--------------------------------------------------------------------------
            */

            $table->string('business_name')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Seller Categories
            |--------------------------------------------------------------------------
            |
            | Multiple categories are allowed, so JSON is used.
            |
            */

            $table->json('categories')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Registration Status
            |--------------------------------------------------------------------------
            |
            | draft     = still filling out registration
            | submitted = completed Step 3
            | approved  = approved by admin
            | rejected  = rejected by admin
            |
            */

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
            ])->default('draft');


            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_registrations');
    }
};