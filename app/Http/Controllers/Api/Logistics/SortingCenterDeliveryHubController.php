<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SortingCenterDeliveryHubController
{
    public function index(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $hubs = $this->hubsFor($center, $request)
            ->orderBy('hub_name')
            ->get()
            ->map(fn (LogisticsDeliveryHub $hub): array => $this->serializeHub($hub));

        return response()->json(['data' => $hubs->values()]);
    }

    public function show(Request $request, LogisticsDeliveryHub $hub): JsonResponse
    {
        $center = $this->center($request);
        abort_unless($hub->logistics_branch_id === $center->id && $hub->archived_at === null, 404);

        return response()->json(['data' => $this->serializeHub($hub)]);
    }

    public function page(Request $request): View
    {
        $center = $this->center($request);
        $hubs = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $center->id)
            ->whereNull('archived_at')
            ->orderBy('hub_name')
            ->get()
            ->map(fn (LogisticsDeliveryHub $hub): array => $this->serializeHub($hub));

        return view('pages.logistics.sorting-center.delivery-hubs', [
            'sortingCenterId' => $center->id,
            'deliveryHubs' => $hubs->values(),
        ]);
    }

    private function hubsFor(LogisticsBranch $center, Request $request): Builder
    {
        $query = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $center->id)
            ->whereNull('archived_at');

        if ($request->filled('status') && in_array($request->string('status')->toString(), ['active', 'inactive'], true)) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->toString().'%';
            $query->where(fn (Builder $builder) => $builder
                ->where('hub_name', 'like', $term)
                ->orWhere('municipality', 'like', $term)
                ->orWhere('province', 'like', $term)
                ->orWhere('manager_name', 'like', $term)
                ->orWhere('manager_email', 'like', $term));
        }

        return $query;
    }

    private function serializeHub(LogisticsDeliveryHub $hub): array
    {
        $center = $hub->logisticsBranch;
        $riders = Rider::query()
            ->where('logistics_branch_id', $center->id)
            ->where('registration_status', 'active')
            ->whereIn('municipal_hub', [$hub->hub_name, $hub->municipality])
            ->whereHas('user', fn (Builder $query) => $query->where('registration_status', 'active'));

        return [
            'id' => $hub->id,
            'hub_name' => $hub->hub_name,
            'center_id' => $center->id,
            'center_name' => $center->name,
            'province' => $hub->province,
            'city' => $hub->municipality,
            'municipality' => $hub->municipality,
            'barangay' => $hub->barangay,
            'postal_code' => $hub->postal_code,
            'house_no' => $hub->house_no,
            'street' => $hub->street,
            'building' => $hub->building,
            'subdivision' => $hub->subdivision,
            'manager_name' => $hub->manager_name,
            'manager_email' => $hub->manager_email,
            'manager_phone' => $hub->manager_phone,
            'login_email' => $hub->login_email,
            'status' => $hub->status,
            'riders_count' => (clone $riders)->count(),
            'couriers_count' => (clone $riders)->whereNotNull('courier_company')->where('courier_company', '<>', '')->count(),
            'vehicles_count' => (clone $riders)->whereNotNull('vehicle')->where('vehicle', '<>', '')->count(),
        ];
    }

    private function center(Request $request): LogisticsBranch
    {
        return LogisticsBranch::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();
    }
}
