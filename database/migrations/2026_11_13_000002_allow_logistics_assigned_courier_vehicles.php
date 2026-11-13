<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table): void {
            $table->string('vehicle')->nullable()->change();
            $table->string('plate_number', 50)->nullable()->change();
        });

        Schema::table('courier_registrations', function (Blueprint $table): void {
            $table->string('vehicle', 80)->nullable()->default(null)->change();
            $table->string('plate_number', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Existing courier profiles may not have personal vehicle details.
    }
};
