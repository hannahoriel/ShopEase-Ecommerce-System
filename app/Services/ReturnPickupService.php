<?php

namespace App\Services;

use App\Http\Services\RiderOperationsService;
use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReturnPickupService
{
    public function riders(LogisticsDeliveryHub $hub, RiderOperationsService $operations): Collection
    {
        return Rider::query()->where('registration_status', 'active')
            ->where('logistics_branch_id', $hub->logistics_branch_id)
            ->whereIn('user_id', DeliveryHubRiderRegistration::query()
                ->where('logistics_delivery_hub_id', $hub->id)->where('status', 'approved')->select('user_id'))
            ->with('user:id,name')->get()
            ->filter(fn (Rider $rider) => $operations->supportsOperation($rider, 'pickup'));
    }

    public function schedule(OrderReturn $return, LogisticsDeliveryHub $hub, User $actor, array $data): OrderReturn
    {
        return DB::transaction(function () use ($return, $hub, $actor, $data): OrderReturn {
            $locked = $this->lockReturn($return);
            $this->authorizeHub($locked, $hub);
            abort_unless($locked->canSchedulePickup(assignRider: true), 409, 'This return cannot be scheduled.');
            $this->authorizeAttempt($locked, $data['attempt']);
            abort_if(Carbon::parse($data['pickup_date'].' '.$data['pickup_time'])->lte(now()), 422, 'Choose a future pickup date and time.');
            $rider = $this->riders($hub, app(RiderOperationsService::class))->firstWhere('id', (int) $data['rider_id']);
            abort_unless($rider, 422, 'Choose an active pickup rider assigned to this delivery hub.');

            $locked->forceFill([
                'pickup_date' => $data['pickup_date'],
                'pickup_time' => $data['pickup_time'],
                'pickup_address' => $locked->order->delivery_address,
                'pickup_rider_id' => $rider->id,
                'shipping_status' => 'scheduled',
                'courier' => $locked->order->logisticsCompany?->business_name ?: $locked->order->shipment?->courier,
                'tracking_number' => $locked->order?->shipment?->tracking_number
                    ?: $locked->tracking_number ?: 'SE-RETURN-'.str_pad((string) $locked->id, 8, '0', STR_PAD_LEFT),
            ])->save();
            $message = "Return pickup attempt {$data['attempt']} scheduled for {$data['pickup_date']} at {$data['pickup_time']}.";
            app(ReturnComplaintService::class)->log($locked, $actor, 'scheduled', $message);
            app(ReturnComplaintService::class)->notify($locked, 'Return pickup scheduled', $message);

            return $locked->fresh('events');
        });
    }

    public function scheduleByBuyer(OrderReturn $return, User $buyer, array $data): OrderReturn
    {
        return DB::transaction(function () use ($return, $buyer, $data): OrderReturn {
            $locked = $this->lockReturn($return);
            abort_unless((int) $locked->order->buyer_id === (int) $buyer->id, 404);
            abort_unless($locked->canSchedulePickup(), 409, 'This return cannot be scheduled.');
            $this->authorizeAttempt($locked, (int) $locked->failed_pickup_attempts + 1);
            abort_if(Carbon::parse($data['pickup_date'].' '.$data['pickup_time'])->lte(now()), 422, 'Choose a future pickup date and time.');

            $profile = $locked->order->buyer?->buyerProfile;
            $province = trim((string) ($locked->order->delivery_province ?: $profile?->province));
            $municipality = trim((string) ($locked->order->delivery_municipality ?: $profile?->municipality));
            abort_if($province === '' || $municipality === '', 422, 'The buyer delivery location is incomplete. Update the delivery address before scheduling pickup.');

            $hubQuery = LogisticsDeliveryHub::query()->where('status', 'active')->whereNull('archived_at')
                ->whereRaw('LOWER(TRIM(province)) = ?', [mb_strtolower($province)])
                ->whereRaw('LOWER(TRIM(municipality)) = ?', [mb_strtolower($municipality)]);
            if ($locked->order->logistics_id) {
                $hubQuery->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $locked->order->logistics_id));
            }
            $hub = $hubQuery->orderBy('id')->first();
            abort_unless($hub, 409, 'There is no active delivery hub for the buyer’s location. Contact ShopEase support.');
            $barangay = trim((string) ($locked->order->delivery_barangay ?: $buyer->buyerProfile?->barangay));
            abort_if($barangay === '', 422, 'Add your barangay to your buyer profile before scheduling pickup.');

            $rider = $this->riders($hub, app(RiderOperationsService::class))->first(fn (Rider $candidate) =>
                in_array(mb_strtolower($barangay), array_filter([
                    mb_strtolower(trim((string) $candidate->assignment_barangay)),
                    mb_strtolower(trim((string) $candidate->barangay)),
                ]), true)
            );
            abort_unless($rider, 409, 'There is no active pickup rider assigned to your barangay yet. Contact ShopEase support.');

            $locked->forceFill([
                'pickup_date' => $data['pickup_date'],
                'pickup_time' => $data['pickup_time'],
                'pickup_address' => $locked->order->delivery_address,
                'pickup_rider_id' => $rider->id,
                'return_delivery_hub_id' => $hub->id,
                'shipping_status' => 'scheduled',
                'courier' => $locked->order->logisticsCompany?->business_name ?: $locked->order->shipment?->courier,
                'tracking_number' => $locked->order?->shipment?->tracking_number
                    ?: $locked->tracking_number ?: 'SE-RETURN-'.str_pad((string) $locked->id, 8, '0', STR_PAD_LEFT),
            ])->save();
            $message = 'Buyer scheduled return pickup attempt '.((int) $locked->failed_pickup_attempts + 1).' for '.$data['pickup_date'].' at '.$data['pickup_time'].'. Assigned pickup rider: '.($rider->user?->name ?: 'Rider').'.';
            app(ReturnComplaintService::class)->log($locked, $buyer, 'scheduled', $message);
            app(ReturnComplaintService::class)->notify($locked, 'Return pickup scheduled', 'Order '.$locked->order->order_number.': your return pickup was scheduled and assigned to a barangay pickup rider.');

            return $locked->fresh('events');
        });
    }

    public function recordOutcome(OrderReturn $return, User $actor, array $data, ?LogisticsDeliveryHub $hub = null, ?Rider $rider = null): OrderReturn
    {
        return DB::transaction(function () use ($return, $actor, $data, $hub, $rider): OrderReturn {
            $locked = $this->lockReturn($return);
            if ($hub) {
                $this->authorizeHub($locked, $hub);
            } else {
                $hubId = $locked->return_delivery_hub_id ?: $locked->order->shipment?->logistics_delivery_hub_id;
                abort_unless($rider && $hubId && (int) $locked->pickup_rider_id === (int) $rider->id
                    && DeliveryHubRiderRegistration::where('logistics_delivery_hub_id', $hubId)
                        ->where('user_id', $actor->id)->where('status', 'approved')->exists(), 404);
            }
            abort_unless($locked->complaint_id && $locked->status === 'accepted' && $locked->return_type === 'return'
                && $locked->shipping_status === 'scheduled' && $locked->pickup_rider_id && $locked->pickup_date,
                409, 'This pickup attempt is no longer active.');
            $this->authorizeAttempt($locked, $data['attempt']);

            $service = app(ReturnComplaintService::class);
            if ($data['outcome'] === 'picked_up') {
                $locked->forceFill(['shipping_status' => 'picked_up'])->save();
                $service->log($locked, $actor, 'picked_up', 'Rider collected the return parcel.');
                $service->notify($locked, 'Return picked up', 'The rider collected your return. Refund processing follows confirmation of receipt.');
            } else {
                $failed = (int) $locked->failed_pickup_attempts + 1;
                $cancelled = $failed >= OrderReturn::MAX_PICKUP_ATTEMPTS;
                $locked->forceFill([
                    'failed_pickup_attempts' => $failed,
                    'shipping_status' => $cancelled ? 'cancelled' : 'pickup_failed',
                    'status' => $cancelled ? 'cancelled' : 'accepted',
                ])->save();

                $message = "Pickup attempt {$failed} of ".OrderReturn::MAX_PICKUP_ATTEMPTS." failed: {$data['reason']}";
                $service->log($locked, $actor, 'pickup_failed', $message);
                if ($cancelled) {
                    $locked->complaint()->update(['status' => 'resolved']);
                    $message = 'Return and refund request cancelled after three failed pickups. The complaint has been closed.';
                    $service->log($locked, $actor, 'cancelled', $message);
                }
                $service->notify($locked, $cancelled ? 'Return request cancelled' : 'Return pickup failed', $message);
            }

            return $locked->fresh('events');
        });
    }

    private function lockReturn(OrderReturn $return): OrderReturn
    {
        $order = Order::query()->whereKey($return->order_id)->lockForUpdate()->firstOrFail();
        $locked = OrderReturn::query()->whereKey($return->id)->lockForUpdate()->firstOrFail();

        return $locked->setRelation('order', $order);
    }

    private function authorizeHub(OrderReturn $return, LogisticsDeliveryHub $hub): void
    {
        $hubId = $return->return_delivery_hub_id ?: $return->order->shipment?->logistics_delivery_hub_id;
        abort_unless((int) $hubId === (int) $hub->id, 404);
    }

    private function authorizeAttempt(OrderReturn $return, int $attempt): void
    {
        abort_unless($attempt === (int) $return->failed_pickup_attempts + 1, 409, 'This pickup attempt has changed. Refresh and try again.');
    }
}
