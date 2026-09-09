<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['buyer', 'seller'])
            ->where('registration_status', 'pending')
            ->orderBy('id')
            ->get()
            ->each(function ($user): void {
                if (DB::table('registrations')->where('user_id', $user->id)->exists()) {
                    return;
                }

                DB::table('registrations')->insert([
                    'user_id' => $user->id,
                    'user_type' => $user->role,
                    'last_name' => $user->last_name ?: 'Unknown',
                    'first_name' => $user->first_name ?: $user->name,
                    'middle_name' => $user->middle_initial,
                    'sex' => in_array($user->sex, ['male', 'female'], true) ? $user->sex : 'male',
                    'birthdate' => $user->birthday ?: now()->toDateString(),
                    'email' => $user->email,
                    'phone' => $user->contact_no ?: '',
                    'password' => $user->password,
                    'province' => $user->province ?: '',
                    'municipality' => $user->municipality ?: '',
                    'barangay' => $user->barangay ?: '',
                    'street' => $user->street ?: '',
                    'house_no' => $user->house_number,
                    'zip_code' => '0000',
                    'business_name' => $user->business_name,
                    'business_category' => $user->line_of_business,
                    'business_permit_path' => $user->upload_business_permit,
                    'valid_id_path' => $user->upload_id ?: '',
                    'status' => 'pending',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('registrations')
            ->whereIn('user_id', DB::table('users')
                ->whereIn('role', ['buyer', 'seller'])
                ->pluck('id'))
            ->where('status', 'pending')
            ->delete();
    }
};
