<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table): void {
            $table->string('registration_type', 20)->default('rider');
            $table->string('assignment_region')->nullable();
            $table->string('assignment_province')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table): void {
            $table->dropColumn([
                'registration_type',
                'assignment_region',
                'assignment_province',
            ]);
        });
    }
};
