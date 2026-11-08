<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\Shipment;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsTransferManifest;
use App\Models\Logistics\SortingCenterVehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SortingCenterDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $center = LogisticsBranch::query()->where('user_id', $request->user()->id)->whereNull('archived_at')->firstOrFail();
        if ($request->filled('tracking')) {
            $tracking = $request->validate(['tracking' => ['required', 'string', 'max:80']])['tracking'];
            $shipment = Shipment::query()->where('tracking_number', $tracking)
                ->where(function ($query) use ($center): void {
                    $query->where('logistics_branch_id', $center->id)
                        ->orWhereHas('transferManifests', fn ($manifest) => $manifest->where(fn ($scope) => $scope
                            ->where(fn ($origin) => $origin->where('origin_type', 'sorting_center')->where('origin_id', $center->id))
                            ->orWhere(fn ($destination) => $destination->where('destination_type', 'sorting_center')->where('destination_id', $center->id))));
                })
                ->with(['order:id,status', 'scans'])->first();
            abort_unless($shipment, 404, 'No parcel with that tracking number is recorded at this sorting center.');
            return response()->json(['data' => ['tracking' => $shipment->tracking_number, 'status' => $shipment->scans->first()?->status ?: $shipment->order?->status ?: 'In transit', 'location' => $shipment->current_location, 'updated_at' => $shipment->location_updated_at?->timezone('Asia/Manila')->format('M d, Y g:i A')]]);
        }

        $hubs = $center->deliveryHubs()->whereNull('archived_at')->orderBy('hub_name')->get();
        $hubRows = $center->deliveryHubs()->whereNull('archived_at')->orderBy('hub_name')->get()->map(function ($hub): array {
            $parcelCount = Shipment::query()->where('logistics_delivery_hub_id', $hub->id)->count();
            return ['name' => $hub->hub_name, 'municipality' => $hub->municipality, 'manager' => $hub->manager_name, 'status' => $hub->status, 'parcels' => $parcelCount];
        })->values();
        $centerShipments = Shipment::query()->where('logistics_branch_id', $center->id)->with(['order', 'scans'])->get();
        $incoming = LogisticsTransferManifest::query()->where('destination_type', 'sorting_center')->where('destination_id', $center->id)->with('shipments')->latest('created_at')->take(10)->get();
        $departed = LogisticsTransferManifest::query()->where('origin_type', 'sorting_center')->where('origin_id', $center->id)->whereNotNull('departed_at')->withCount('shipments')->latest('departed_at')->take(10)->get();
        $disputes = $centerShipments->filter(function (Shipment $shipment): bool {
            $status = mb_strtolower((string) ($shipment->scans->first()?->status ?? $shipment->order?->status));
            return str_contains($status, 'lost') || str_contains($status, 'damage') || str_contains($status, 'exception') || str_contains($status, 'dispute');
        })->count();
        $vehicles = SortingCenterVehicle::query()->where('sorting_center_id', $center->id)->count();
        $announcementRows = Announcement::publishedForAudience(['All Users', 'Logistics'])
            ->latest('published_at')->take(8)->get()->map(fn ($item) => ['badge_label' => 'ANNOUNCEMENT', 'title' => $item->title, 'body' => $item->body])->values();

        return response()->json(['data' => [
            'center' => ['id' => $center->id, 'name' => $center->name],
            'stats' => [
                'parcels_at_center' => $centerShipments->filter(fn (Shipment $shipment) => ! in_array($shipment->order?->status, ['completed', 'delivered', 'cancelled'], true))->count(),
                'delivery_hubs' => $hubs->count(), 'vehicles' => $vehicles, 'parcel_disputes' => $disputes,
                'incoming_transfers' => $incoming->sum(fn ($manifest) => $manifest->shipments->filter(fn (Shipment $shipment) => $shipment->pivot?->received_at === null)->count()),
                'departed_parcels' => $departed->sum('shipments_count'),
            ],
            'hubs' => $hubRows,
            'announcements' => $announcementRows,
        ]]);
    }
}
