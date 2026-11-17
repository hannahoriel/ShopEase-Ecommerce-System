<?php

use App\Mail\AccountStatusChanged;
use App\Models\Admin\Announcement;
use App\Models\Admin\Order;
use App\Models\Admin\OrderStatusHistory;
use App\Models\Buyer\BuyerNotification;
use App\Models\Admin\PlatformPolicy;
use App\Models\Logistics\DeliveryHubParcelIssue;
use App\Models\Logistics\SortingCenterParcelIssue;
use App\Models\Seller\Seller;
use App\Models\User;
use App\Services\CodSettlementService;
use App\Services\SellerPickupAttemptService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

        if ($user->role === User::ROLE_SELLER) {
            Seller::query()
                ->where('user_id', $user->id)
                ->where('registration_status', 'suspended')
                ->update(['registration_status' => 'active']);
        }

        $user->email && Mail::to($user->email)->send(new AccountStatusChanged(
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

Artisan::command('platform-settings:publish-scheduled', function () {
    $announcements = Announcement::where('status', 'Scheduled')
        ->where('published_at', '<=', now())
        ->update(['status' => 'Published', 'is_active' => true]);

    $policies = PlatformPolicy::where('status', 'Scheduled')
        ->where('published_at', '<=', now())
        ->update(['status' => 'Published']);

    $this->info("Published {$announcements} announcement(s) and {$policies} policy/policies.");
})->purpose('Publish platform announcements and policies when their schedule is due');

Schedule::command('platform-settings:publish-scheduled')->everyMinute();

Artisan::command('orders:auto-confirm-delivered', function () {
    $cutoff = now()->subDays(7);
    $confirmed = 0;

    Order::query()->where('status', 'delivered')
        ->where(function ($query) use ($cutoff): void {
            $query->whereHas('shipment', fn ($shipment) => $shipment
                ->whereNotNull('delivered_at')->where('delivered_at', '<=', $cutoff))
                ->orWhereHas('statusHistory', fn ($history) => $history
                    ->where('to_status', 'delivered')->where('created_at', '<=', $cutoff));
        })
        ->with(['shipment', 'statusHistory'])
        ->chunkById(100, function ($orders) use ($cutoff, &$confirmed): void {
            foreach ($orders as $order) {
                $deliveryEvent = $order->statusHistory->firstWhere('to_status', 'delivered');
                $deliveredAt = $order->shipment?->delivered_at ?: $deliveryEvent?->created_at;
                if (! $deliveredAt || $deliveredAt->isAfter($cutoff)) {
                    continue;
                }

                DB::transaction(function () use ($order, &$confirmed): void {
                    $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->first();
                    if (! $lockedOrder || $lockedOrder->status !== 'delivered') {
                        return;
                    }
                    $lockedOrder->load(['shipment', 'statusHistory']);
                    $deliveryEvent = $lockedOrder->statusHistory->firstWhere('to_status', 'delivered');
                    $deliveredAt = $lockedOrder->shipment?->delivered_at ?: $deliveryEvent?->created_at;
                    if (! $deliveredAt || $deliveredAt->copy()->addDays(7)->isFuture()) {
                        return;
                    }

                    $receivedAt = $deliveredAt->copy()->addDays(7);
                    $lockedOrder->update([
                        'status' => 'completed',
                        'buyer_received_at' => $receivedAt,
                        'buyer_received_automatically' => true,
                    ]);
                    OrderStatusHistory::query()->create([
                        'order_id' => $lockedOrder->id,
                        'from_status' => 'delivered',
                        'to_status' => 'completed',
                        'changed_by' => null,
                        'notes' => 'Automatically marked received seven days after delivery because the buyer did not confirm.',
                    ]);
                    BuyerNotification::createForOrderStatus($lockedOrder, 'completed');
                    app(CodSettlementService::class)->releaseSellerEarnings((int) $lockedOrder->id);
                    $confirmed++;
                });
            }
        });

    $this->info("Automatically confirmed {$confirmed} delivered order(s).");
})->purpose('Mark delivered orders as received after seven days without buyer confirmation');

Schedule::command('orders:auto-confirm-delivered')->everyMinute()->withoutOverlapping();

Artisan::command('returns:auto-confirm-receipts', function (\App\Services\ReturnReceiptService $receipts): void {
    \App\Models\Admin\OrderReturn::query()
        ->where('status', 'accepted')
        ->where('return_type', 'return')
        ->whereIn('shipping_status', ['delivered', 'returned'])
        ->whereNull('returned_at')
        ->whereHas('events', fn ($query) => $query->where('status', 'delivered')->where('scanned_at', '<=', now()->subHours(72)))
        ->eachById(fn ($return) => $receipts->confirm($return));
})->purpose('Confirm seller return receipts 72 hours after delivery');

Schedule::command('returns:auto-confirm-receipts')->everyMinute()->withoutOverlapping();

Artisan::command('cod:settle-eligible-seller-earnings', function (): void {
    $released = app(CodSettlementService::class)->releaseEligibleSellerEarnings();
    $this->info("Released {$released} eligible COD seller settlement(s).");
})->purpose('Release held COD seller earnings after delivery confirmation and case resolution');

Schedule::command('cod:settle-eligible-seller-earnings')->everyMinute()->withoutOverlapping();

Artisan::command('orders:process-missed-seller-pickups', function (SellerPickupAttemptService $pickups): void {
    $result = $pickups->processMissedPickups();
    $this->info(
        "Processed {$result['missed']} missed seller pickup(s); "
        ."cancelled {$result['cancelled']} order(s) and posted {$result['deducted']} rider deduction(s).",
    );
})->purpose('Record seller pickup no-shows at 6 PM and cancel orders after three failed attempts');

Schedule::command('orders:process-missed-seller-pickups')
    ->dailyAt('18:00')
    ->timezone('Asia/Manila')
    ->withoutOverlapping();

Artisan::command('logistics:reconcile-parcel-issues', function (): void {
    $sorting = SortingCenterParcelIssue::query()->whereIn('issue_type', ['Lost Parcel', 'Damaged Parcel'])->whereNull('decision')->count();
    $hubs = DeliveryHubParcelIssue::query()->whereIn('issue_type', ['Lost Parcel', 'Damaged Parcel'])->whereNull('decision')->count();
    $this->info("Pending admin review: {$sorting} sorting-center report(s), {$hubs} delivery-hub report(s). No wallet or order changes were made.");
})->purpose('List unresolved parcel issue reports awaiting an admin decision');
