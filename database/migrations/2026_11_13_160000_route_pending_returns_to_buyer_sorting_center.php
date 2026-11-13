<?php

use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsTransferManifest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('logistics_transfer_manifests', 'order_return_id')) {
            return;
        }

        LogisticsTransferManifest::query()
            ->whereNotNull('order_return_id')
            ->where('destination_type', 'sorting_center')
            ->where('status', 'loaded')
            ->with(['orderReturn.order.buyer.buyerProfile', 'orderReturn.returnDeliveryHub.logisticsBranch', 'shipments'])
            ->chunkById(100, function ($manifests): void {
                foreach ($manifests as $manifest) {
                    $return = $manifest->orderReturn;
                    $order = $return?->order;
                    if (! $return || ! $order || $return->shipping_status !== 'in_transit_to_sorting_center') {
                        continue;
                    }

                    $unreceivedShipments = $manifest->shipments
                        ->filter(fn ($shipment): bool => $shipment->pivot->received_at === null);
                    if ($unreceivedShipments->isEmpty()) {
                        continue;
                    }

                    $province = trim((string) ($order->delivery_province ?: $order->buyer?->buyerProfile?->province));
                    if ($province === '') {
                        continue;
                    }

                    $logisticsId = $return->returnDeliveryHub?->logisticsBranch?->logistics_id;
                    $destination = LogisticsBranch::query()
                        ->whereNull('archived_at')
                        ->where('status', 'active')
                        ->when($logisticsId, fn ($query) => $query->where('logistics_id', $logisticsId))
                        ->whereRaw('LOWER(province) = ?', [mb_strtolower($province)])
                        ->orderBy('id')
                        ->first();

                    if (! $destination) {
                        continue;
                    }

                    $manifest->update(['destination_id' => $destination->id]);
                    foreach ($unreceivedShipments as $shipment) {
                        $shipment->update([
                            'logistics_branch_id' => $destination->id,
                            'logistics_delivery_hub_id' => null,
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // The original destination cannot be safely inferred after rerouting.
    }
};
