<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class AdminFirstLoginPreviewSeeder extends Seeder
{
    public const EMAIL = 'admin-first-login@shopease.test';
    public const TEMPORARY_PASSWORD = 'ShopEasePreview2026!';

    public function run(): void
    {
        if (! app()->environment('local') || ! in_array(config('database.connections.'.config('database.default').'.host'), ['127.0.0.1', 'localhost', '::1'], true)) {
            throw new RuntimeException('This demo seeder requires the local ShopEase database.');
        }

        $name = 'Admin First Login Preview';
        $user = User::firstOrNew(['email' => self::EMAIL]);
        if ($user->exists && ($user->role !== User::ROLE_ADMIN || $user->name !== $name)) {
            throw new RuntimeException('The preview email is already assigned to another account.');
        }

        $user->forceFill([
            'name' => $name,
            'role' => User::ROLE_ADMIN,
            'registration_status' => 'active',
            'email_verified_at' => now(),
            'password' => self::TEMPORARY_PASSWORD,
            'must_change_password' => true,
            'remember_token' => Str::random(60),
        ])->save();
    }
}
