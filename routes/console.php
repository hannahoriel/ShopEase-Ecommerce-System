<?php

use App\Mail\AccountStatusChanged;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:reactivate-expired', function () {
    $expiredSuspensions = User::query()
        ->where('registration_status', 'suspended')
        ->whereNotNull('suspended_until')
        ->where('suspended_until', '<=', now())
        ->get();

    $reactivated = 0;

    foreach ($expiredSuspensions as $user) {
        $user->update([
            'registration_status' => 'active',
            'suspended_until' => null,
            'account_action_reason' => null,
            'account_action_details' => null,
        ]);

        $user->email && Mail::to($user->email)->send(new \App\Mail\AccountStatusChanged(
            user: $user,
            status: 'active',
            reason: null,
            details: null,
            duration: null,
        ));

        $reactivated++;
    }

    $this->info("Reactivated {$reactivated} expired suspension(s).");
})->purpose('Reactivate users whose suspension period has ended');

Schedule::command('users:reactivate-expired')->everyMinute();
