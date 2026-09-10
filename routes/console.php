<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:reactivate-expired', function () {
    $reactivated = User::query()
        ->where('registration_status', 'suspended')
        ->whereNotNull('suspended_until')
        ->where('suspended_until', '<=', now())
        ->update([
            'registration_status' => 'active',
            'suspended_until' => null,
            'account_action_reason' => null,
            'account_action_details' => null,
        ]);

    $this->info("Reactivated {$reactivated} expired suspension(s).");
})->purpose('Reactivate users whose suspension period has ended');

Schedule::command('users:reactivate-expired')->everyMinute();
