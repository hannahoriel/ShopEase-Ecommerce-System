<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $password = Hash::make('Admin@123');

        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Shop',
                'middle_name' => null,
                'last_name' => 'Ease',
                'phone_number' => '0912345678',
                'password' => $password,
                'profile_picture' => null,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Shop Ease',
                'first_name' => 'Shop',
                'last_name' => 'Ease',
                'contact_no' => '0912345678',
                'role' => 'admin',
                'password' => $password,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('admins')->where('email', 'admin@gmail.com')->delete();
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
    }
};
