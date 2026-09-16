<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE registrations MODIFY user_type ENUM('seller', 'buyer', 'logistics', 'rider') NOT NULL");
        DB::statement("ALTER TABLE registrations MODIFY sex ENUM('male', 'female', 'other') NOT NULL");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE registrations MODIFY user_type ENUM('seller', 'buyer') NOT NULL");
        DB::statement("ALTER TABLE registrations MODIFY sex ENUM('male', 'female') NOT NULL");
    }
};