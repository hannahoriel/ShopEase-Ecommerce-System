<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SellerNotificationPreferences;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(NotificationSending::class, function (NotificationSending $event): ?bool {
            if ($event->channel !== 'database' || ! ($event->notifiable instanceof User)) {
                return null;
            }

            $allowed = app(SellerNotificationPreferences::class)
                ->allows($event->notifiable, $event->notification);

            return $allowed ? null : false;
        });

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
