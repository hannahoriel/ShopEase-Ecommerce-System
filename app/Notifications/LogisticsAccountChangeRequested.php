<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LogisticsAccountChangeRequested extends Notification
{
    use Queueable;

    public function __construct(public int $requestId, public string $companyName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Logistics Account Information Change Request',
            'message' => $this->companyName.' requested a change to their account information. Please review the request.',
            'type' => 'logistics_account_change_request',
            'request_id' => $this->requestId,
            'category' => 'logistics',
            'url' => '/admin/logistics-management',
        ];
    }
}
