<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === User::ROLE_LOGISTICS, 403);

        $logistics = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();

        $branches = LogisticsBranch::query()
            ->where('logistics_id', $logistics->id)
            ->whereNull('archived_at')
            ->get(['id', 'name']);
        $branchIds = $branches->modelKeys();
        $hubIds = LogisticsDeliveryHub::query()
            ->whereIn('logistics_branch_id', $branchIds ?: [-1])
            ->whereNull('archived_at')
            ->pluck('id')
            ->all();

        $shipmentQuery = Shipment::query()->where(function ($scope) use ($logistics, $branchIds, $hubIds): void {
            $hasFacilityScope = false;
            if ($branchIds !== []) {
                $scope->whereIn('logistics_branch_id', $branchIds);
                $hasFacilityScope = true;
            }
            if ($hubIds !== []) {
                $scope->{$hasFacilityScope ? 'orWhereIn' : 'whereIn'}('logistics_delivery_hub_id', $hubIds);
                $hasFacilityScope = true;
            }

            $scope->{$hasFacilityScope ? 'orWhereHas' : 'whereHas'}('rider.logisticsBranch', fn ($branch) => $branch
                ->where('logistics_id', $logistics->id)
                ->whereNull('archived_at'));
        });

        $activeRiders = Rider::query()
            ->whereIn('logistics_branch_id', $branchIds ?: [-1])
            ->where('registration_status', 'active')
            ->whereHas('user', fn ($query) => $query->where('registration_status', 'active'));

        $dashboardStats = [
            'sorting_centers' => $branches->count(),
            'delivery_hubs' => count($hubIds),
            'riders' => (clone $activeRiders)->count(),
            'drivers' => (clone $activeRiders)->whereNotNull('courier_company')->where('courier_company', '<>', '')->count(),
            'vehicles' => (clone $activeRiders)->whereNotNull('vehicle')->where('vehicle', '<>', '')->count(),
            'parcels_in_network' => (clone $shipmentQuery)->count(),
            'recorded_shipping_fees' => (clone $shipmentQuery)
                ->where(function ($query): void {
                    $query->whereNotNull('delivered_at')
                        ->orWhereHas('order', fn ($order) => $order->whereIn('status', ['delivered', 'completed']));
                })
                ->sum('shipping_fee'),
        ];

        $dashboardAnnouncements = Announcement::publishedForAudience(['All Users', 'Logistics'])->get();

        return view('pages.logistics.dashboard', [
            'companyDashboardTitle' => filled($logistics->business_name)
                ? trim($logistics->business_name).' Dashboard'
                : 'Company Dashboard',
            'logisticsCompanyName' => $logistics->business_name,
            'dashboardStats' => $dashboardStats,
            'dashboardAnnouncements' => $dashboardAnnouncements,
        ]);
    }
}
