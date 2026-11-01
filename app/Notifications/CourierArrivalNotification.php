<?php

namespace App\Notifications;

use App\Models\Logistics\LogisticsTransferManifest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourierArrivalNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly LogisticsTransferManifest $manifest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'courier_arrival',
            'title' => 'Courier arrived',
            'message' => 'Trip '.$this->manifest->batch_key.' has arrived. Scan and reconcile each parcel before completing the transfer.',
            'manifest_id' => $this->manifest->id,
            'trip_reference' => $this->manifest->batch_key,
        ];
    }
}
