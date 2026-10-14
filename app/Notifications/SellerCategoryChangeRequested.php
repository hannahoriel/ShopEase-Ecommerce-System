<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SellerCategoryChangeRequested extends Notification
{
    use Queueable;

    public function __construct(public int $requestId, public string $shopName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Seller Category Change Request',
            'message' => $this->shopName.' requested a change to their registered product categories. Please review the request.',
            'type' => 'seller_category_change_request',
            'request_id' => $this->requestId,
            'category' => 'seller_compliance',
            'url' => '/admin/seller-compliance',
        ];
    }
}
