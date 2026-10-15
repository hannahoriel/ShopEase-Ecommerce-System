<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SellerDocumentChangeRequested extends Notification
{
    use Queueable;

    public function __construct(
        public int $requestId,
        public string $shopName,
        public string $requestType,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $documentName = match ($this->requestType) {
            'replace_valid_id' => 'Valid ID replacement',
            'replace_business_permit' => 'business permit replacement',
            'add_business_permit' => 'new business permit',
            default => 'document change',
        };

        return [
            'title' => 'Seller Document Change Request',
            'message' => "{$this->shopName} submitted a {$documentName} request for admin review.",
            'type' => 'seller_document_change_request',
            'request_id' => $this->requestId,
            'category' => 'seller_compliance',
            'url' => '/admin/notifications',
        ];
    }
}
