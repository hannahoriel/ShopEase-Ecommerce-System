<?php

namespace App\Http\Services;

use App\Models\Admin\Order;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use Illuminate\Support\Facades\DB;

class AssignScheduledPickups
{
    public function forRider(Rider $rider): void
    {
        $operation = preg_replace('/[^a-z]/i', '', (string) $rider->logistics_operation);
        if (! preg_match('/pickup/i', $operation)) {
            return;
        }

        $province = mb_strtolower(trim((string) ($rider->assignment_province ?: $rider->province)));
        $municipality = mb_strtolower(trim((string) $rider->municipal_hub));
        $barangay = mb_strtolower(trim((string) ($rider->assignment_barangay ?: $rider->barangay)));
        if ($province === '' || $municipality === '' || $barangay === '') {
            return;
        }

        $hubIds = LogisticsDeliveryHub::query()
            ->where('logistics_branch_id', $rider->logistics_branch_id)
            ->whereNull('archived_at')
            ->whereRaw('LOWER(province) = ?', [$province])
            ->where(function ($query) use ($municipality): void {
                $query->whereRaw('LOWER(hub_name) = ?', [$municipality])
                    ->orWhereRaw('LOWER(municipality) = ?', [$municipality]);
            })
            ->pluck('id');
        if ($hubIds->isEmpty()) {
            return;
        }

        $candidates = Order::query()
            ->where('status', 'to_ship')
            ->whereNotNull('pickup_date')
            ->whereHas('seller', fn ($query) => $query
                ->whereRaw('LOWER(province) = ?', [$province])
                ->whereRaw('LOWER(municipality) = ?', [$municipality]))
            ->whereHas('shipment', fn ($query) => $query->whereNull('rider_id'))
            ->with(['seller:id,province,municipality,barangay'])
            ->limit(200)->get();

        foreach ($candidates as $candidate) {
            $sellerBarangay = mb_strtolower(trim((string) $candidate->seller?->barangay));
            $normalize = fn (string $value): string => preg_replace('/[^a-z]/', '', mb_strtolower(trim($value)));
            if ($sellerBarangay === '' || levenshtein($normalize($barangay), $normalize($sellerBarangay)) > 2) {
                continue;
            }

            DB::transaction(function () use ($candidate, $rider, $hubIds): void {
                $shipment = Shipment::query()->where('order_id', $candidate->id)->lockForUpdate()->first();
                if ($shipment && $shipment->rider_id === null) {
                    $shipment->update([
                        'rider_id' => $rider->id,
                        'logistics_branch_id' => $rider->logistics_branch_id,
                        'logistics_delivery_hub_id' => $hubIds->first(),
                    ]);
                }
            });
        }
    }
}
