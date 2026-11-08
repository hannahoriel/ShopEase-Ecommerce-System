<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Services\DeliveryHubRiderRegistrationService;
use App\Models\Logistics\DeliveryHubFleetArchive;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryHubFleetController extends Controller
{
    private function hub(Request $request): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();
    }

    private function registrations(LogisticsDeliveryHub $hub): Builder
    {
        return DeliveryHubRiderRegistration::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->where('status', 'approved')
            ->whereHas('user', fn (Builder $query) => $query
                ->where('role', User::ROLE_RIDER)
                ->where('registration_status', 'active')
                ->whereHas('riderProfile', fn (Builder $rider) => $rider
                    ->whereRaw('LOWER(vehicle) IN (?, ?)', ['motorcycle', 'tricycle'])))
            ->with(['user.riderProfile.shipments.order', 'deliveryHub.logisticsBranch.logistics']);
    }

    public function index(Request $request, DeliveryHubRiderRegistrationService $service): JsonResponse
    {
        $hub = $this->hub($request);
        $archives = DeliveryHubFleetArchive::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->latest('archived_at')
            ->get();
        $archivedDrivers = $archives->where('record_type', 'riders')->pluck('record_id')->all();
        $archivedVehicles = $archives->where('record_type', 'vehicles')->pluck('record_id')->all();

        $drivers = $this->registrations($hub)
            ->whereNotIn('id', $archivedDrivers)
            ->latest('reviewed_at')
            ->get()
            ->map(fn (DeliveryHubRiderRegistration $registration): array => $this->presentDriver($registration, $service->present($registration)))
            ->values();

        $vehicles = $this->registrations($hub)
            ->whereNotIn('id', $archivedVehicles)
            ->latest('reviewed_at')
            ->get()
            ->map(fn (DeliveryHubRiderRegistration $registration): array => $this->presentVehicle($registration, $service->present($registration)))
            ->values();

        return response()->json([
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'archives' => $archives->map(fn (DeliveryHubFleetArchive $archive): array => $this->presentArchive($archive))->values(),
        ]);
    }

    public function archive(Request $request, string $type, int $record, DeliveryHubRiderRegistrationService $service): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless(in_array($type, ['vehicles', 'riders'], true), 404);
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $registration = $this->registrations($hub)->findOrFail($record);
        $snapshot = $type === 'vehicles'
            ? $this->presentVehicle($registration, $service->present($registration))
            : $this->presentDriver($registration, $service->present($registration));

        $archive = DB::transaction(function () use ($hub, $type, $registration, $data, $snapshot, $request): DeliveryHubFleetArchive {
            $existing = DeliveryHubFleetArchive::query()
                ->where('logistics_delivery_hub_id', $hub->id)
                ->where('record_type', $type)
                ->where('record_id', $registration->id)
                ->lockForUpdate()
                ->first();
            if ($existing) {
                throw ValidationException::withMessages(['record' => 'This item is already archived.']);
            }

            return DeliveryHubFleetArchive::query()->create([
                'logistics_delivery_hub_id' => $hub->id,
                'record_type' => $type,
                'record_id' => $registration->id,
                'reason' => trim($data['reason']),
                'snapshot' => $snapshot,
                'archived_by' => $request->user()->id,
                'archived_at' => now(),
            ]);
        });

        return response()->json(['message' => 'Record archived.', 'data' => $this->presentArchive($archive)], 201);
    }

    public function restore(Request $request, string $type, int $record): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless(in_array($type, ['vehicles', 'riders'], true), 404);
        $archive = DeliveryHubFleetArchive::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->where('record_type', $type)
            ->where('record_id', $record)
            ->firstOrFail();
        $archive->delete();

        return response()->json(['message' => 'Record restored.']);
    }

    private function presentDriver(DeliveryHubRiderRegistration $registration, array $rider): array
    {
        $rider['assignment_role'] = $this->role($rider['logistics_operation'] ?? null);
        $rider['assignment_barangay'] = $rider['assignment_barangay'] ?: $rider['barangay'];
        $rider['availability'] = $this->isOnDelivery($registration) ? 'On Delivery' : 'Available';

        return $rider;
    }

    private function presentVehicle(DeliveryHubRiderRegistration $registration, array $rider): array
    {
        $type = $rider['vehicle_type'] ?: 'Vehicle';
        $name = trim($type.' · '.($rider['plate_number'] ?? ''));

        return [
            'id' => $rider['id'],
            'name' => $name,
            'vehicle_name' => $type,
            'type' => $type,
            'vehicle_type' => $type,
            'plate_number' => $rider['plate_number'],
            'driver_name' => $rider['full_name'],
            'assigned_driver' => $rider['full_name'],
            'registration_date' => $rider['created_at'],
            'availability' => $this->isOnDelivery($registration) ? 'On Delivery' : 'Available',
            'rider_id' => $rider['user_id'],
            'delivery_hub_name' => $rider['delivery_hub_name'],
            'or_cr_url' => $rider['or_cr'],
        ];
    }

    private function presentArchive(DeliveryHubFleetArchive $archive): array
    {
        return [
            'key' => $archive->record_type.':'.$archive->record_id,
            'type' => $archive->record_type,
            'id' => (string) $archive->record_id,
            'reason' => $archive->reason,
            'archived_at' => $archive->archived_at,
            'snapshot' => $archive->snapshot,
        ];
    }

    private function role(?string $operation): ?string
    {
        $operation = mb_strtolower((string) $operation);
        if (str_contains($operation, 'pickup')) return 'pickup';
        if (str_contains($operation, 'delivery')) return 'delivery';

        return null;
    }

    private function isOnDelivery(DeliveryHubRiderRegistration $registration): bool
    {
        return (bool) $registration->user?->riderProfile?->shipments?->contains(
            fn ($shipment): bool => $shipment->order?->status === 'out_for_delivery',
        );
    }
}
