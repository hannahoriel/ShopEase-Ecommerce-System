<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('user_type', [
                'seller',
                'buyer',
                'logistics',
                'rider',
            ])->change();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('user_type', [
                'seller',
                'buyer',
            ])->change();
        });
    }
};