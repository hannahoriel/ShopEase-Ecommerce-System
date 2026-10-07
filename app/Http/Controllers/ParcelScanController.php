<?php

namespace App\Http\Controllers;

use App\Models\Admin\OrderStatusHistory;
use App\Models\Admin\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParcelScanController extends Controller
{
    private const NEXT_STATUSES = [
        'to_ship' => ['in_transit'],
        'in_transit' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered'],
        'delivered' => [],
    ];

    public function show(string $token): View
    {
        $shipment = $this->shipmentForToken($token);
        $shipment->load('order');

        return view('pages.parcel-scan', [
            'shipment' => $shipment,
            'order' => $shipment->order,
            'nextStatuses' => self::NEXT_STATUSES[$shipment->order->status] ?? [],
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_merge(...array_values(self::NEXT_STATUSES)))],
        ]);

        DB::transaction(function () use ($token, $validated): void {
            $shipment = Shipment::query()
                ->where('scan_token', $token)
                ->lockForUpdate()
                ->firstOrFail();
            $order = $shipment->order()->lockForUpdate()->firstOrFail();
            $allowed = self::NEXT_STATUSES[$order->status] ?? [];
            if (! in_array($validated['status'], $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => 'This parcel status cannot be applied now. Refresh the scan page and try again.',
                ]);
            }

            $previousStatus = $order->status;
            $order->update(['status' => $validated['status']]);
            $shipment->update([
                'current_location' => $validated['location'],
                'picked_up_at' => $validated['status'] === 'in_transit'
                    ? ($shipment->picked_up_at ?? now())
                    : $shipment->picked_up_at,
                'delivered_at' => $validated['status'] === 'delivered'
                    ? now()
                    : $shipment->delivered_at,
            ]);
            $shipment->scans()->create([
                'status' => $validated['status'],
                'location' => $validated['location'],
                'scanned_at' => now(),
            ]);
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $previousStatus,
                'to_status' => $validated['status'],
                'notes' => 'Parcel scan recorded at ' . $validated['location'] . '.',
            ]);
        });

        return to_route('parcel.scan.show', ['token' => $token])
            ->with('success', 'Parcel status and location have been updated.');
    }

    private function shipmentForToken(string $token): Shipment
    {
        return Shipment::query()
            ->where('scan_token', $token)
            ->firstOrFail();
    }
}
