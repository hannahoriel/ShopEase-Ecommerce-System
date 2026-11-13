<?php

use App\Models\Admin\OrderReturn;
use App\Models\Logistics\LogisticsDeliveryHub;
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
            ->where('origin_type', 'delivery_hub')
            ->where('destination_type', 'sorting_center')
            ->where('status', 'loaded')
            ->with(['orderReturn', 'shipments'])
            ->chunkById(100, function ($manifests): void {
                foreach ($manifests as $manifest) {
                    $return = $manifest->orderReturn;
                    if (! $return || $return->shipping_status !== 'in_transit_to_sorting_center') {
                        continue;
                    }

                    $shipment = $manifest->shipments->first(
                        fn ($candidate) => $candidate->pivot->received_at === null,
                    );
                    if (! $shipment) {
                        continue;
                    }

                    $return->forceFill([
                        'shipping_status' => 'received_at_delivery_hub',
                        'return_delivery_hub_id' => $manifest->origin_id,
                    ])->save();
                    $shipment->update([
                        'logistics_branch_id' => null,
                        'logistics_delivery_hub_id' => $manifest->origin_id,
                    ]);
                    $hub = LogisticsDeliveryHub::query()->find($manifest->origin_id);
                    $location = $hub?->hub_name.' · '.$hub?->municipality.', '.$hub?->province;
                    $shipment->scans()->create([
                        'status' => 'Received at Delivery Hub',
                        'location' => $location,
                        'scanned_at' => now(),
                    ]);
                    $return->events()->create([
                        'status' => 'received_at_delivery_hub',
                        'location' => $location,
                        'scanned_at' => now(),
                    ]);
                    $manifest->shipments()->updateExistingPivot($shipment->id, [
                        'load_status' => 'pending',
                        'loaded_at' => null,
                        'loaded_by' => null,
                    ]);
                    $manifest->update([
                        'destination_type' => 'sorting_center',
                        'destination_id' => $manifest->destination_id,
                        'status' => 'preparing',
                        'loaded_at' => null,
                        'departed_at' => null,
                        'arrived_at' => null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Keep received returns at the buyer hub; re-marking them dispatched would lose the physical scan state.
    }
};
