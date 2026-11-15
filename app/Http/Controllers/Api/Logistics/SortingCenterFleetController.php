<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\SortingCenterFleetArchive;
use App\Models\Logistics\SortingCenterVehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SortingCenterFleetController extends Controller
{
    private function center(Request $request): LogisticsBranch
    {
        return LogisticsBranch::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function vehicles(LogisticsBranch $center): Builder
    {
        return SortingCenterVehicle::query()->where('sorting_center_id', $center->id);
    }

    private function couriers(LogisticsBranch $center): Builder
    {
        return CourierRegistration::query()->where('sorting_center_id', $center->id)->where('status', 'approved');
    }

    public function indexVehicles(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $archivedIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', 'vehicles')->pluck('record_id');
        return response()->json(['data' => $this->vehicles($center)->whereNotIn('id', $archivedIds)->latest('created_at')->get()]);
    }

    public function indexCouriers(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $archivedIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', 'couriers')->pluck('record_id');
        $archivedVehicleIds = SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', 'vehicles')->pluck('record_id');
        $couriers = $this->couriers($center)->with('assignedVehicle')
            ->whereNotIn('id', $archivedIds)->latest('reviewed_at')->get();
        $couriers->each(function (CourierRegistration $courier) use ($archivedVehicleIds): void {
            $vehicle = $courier->assignedVehicle;
            if ($vehicle && $archivedVehicleIds->contains($vehicle->id)) {
                $vehicle = null;
            }
            $courier->setAttribute('vehicle', $vehicle?->type);
            $courier->setAttribute('plate_number', $vehicle?->plate_number);
        });
        return response()->json(['data' => $couriers]);
    }

    public function updateCourierVehicle(Request $request, int $courier): JsonResponse
    {
        $center = $this->center($request);
        $registration = $this->couriers($center)->findOrFail($courier);
        abort_if(SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', 'couriers')->where('record_id', $registration->id)->exists(), 404);
        $data = $request->validate([
            'vehicle' => ['required', 'string', 'max:80'],
            'plate_number' => ['required', 'string', 'max:50'],
        ]);
        $vehicle = $registration->assignedVehicle;
        abort_unless($vehicle && $vehicle->sorting_center_id === $center->id, 409,
            'Assign a Sorting Center vehicle to this courier before editing its truck details.');
        abort_if(SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', 'vehicles')->where('record_id', $vehicle->id)->exists(), 409,
            'Restore the assigned Sorting Center vehicle before editing its truck details.');
        $vehicle->update([
            'type' => trim($data['vehicle']),
            'plate_number' => trim($data['plate_number']),
        ]);
        $registration->setAttribute('vehicle', $vehicle->type);
        $registration->setAttribute('plate_number', $vehicle->plate_number);

        return response()->json([
            'message' => 'Courier vehicle details updated.',
            'data' => $registration,
            'vehicle' => $vehicle->refresh(),
        ]);
    }

    public function indexArchives(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $archives = SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)->latest('archived_at')->get();
        return response()->json(['data' => $archives->map(fn (SortingCenterFleetArchive $archive): array => [
            'id' => (string) $archive->record_id,
            'key' => $archive->record_type.':'.$archive->record_id,
            'type' => $archive->record_type,
            'id' => (string) $archive->record_id,
            'reason' => $archive->reason,
            'archived_at' => $archive->archived_at,
            'snapshot' => $archive->snapshot,
        ])]);
    }

    public function storeVehicle(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $data = $request->validate(array_merge($this->vehicleRules(), [
            'courier_registration_id' => ['nullable', 'integer', Rule::exists('courier_registrations', 'id')->where('sorting_center_id', $center->id)->where('status', 'approved')],
        ]));
        $courierId = $data['courier_registration_id'] ?? null;
        unset($data['courier_registration_id']);

        if ($courierId) {
            $courier = $this->couriers($center)->findOrFail($courierId);
            $data['driver_name'] = trim(implode(' ', array_filter([
                $courier->first_name,
                $courier->middle_name,
                $courier->last_name,
            ])));
            $data['courier_registration_id'] = $courier->id;
        }

        $data['sorting_center_id'] = $center->id;
        if (isset($data['or_cr'])) {
            $data['or_cr_path'] = $data['or_cr']->store('sorting-center/vehicles/or-cr', 'public');
            unset($data['or_cr']);
        }
        $data['availability'] ??= 'Available';
        $vehicle = DB::transaction(function () use ($data, $courierId): SortingCenterVehicle {
            $vehicle = SortingCenterVehicle::query()->create($data);
            return $vehicle;
        });

        return response()->json(['message' => 'Vehicle registered.', 'data' => $vehicle], 201);
    }

    public function updateVehicle(Request $request, SortingCenterVehicle $vehicle): JsonResponse
    {
        $center = $this->center($request);
        abort_unless($vehicle->sorting_center_id === $center->id, 404);
        $data = $request->validate(array_merge($this->vehicleRules(true), [
            'courier_registration_id' => ['nullable', 'integer', Rule::exists('courier_registrations', 'id')->where('sorting_center_id', $center->id)->where('status', 'approved')],
        ]));
        $courierId = $data['courier_registration_id'] ?? null;
        unset($data['courier_registration_id']);
        $courier = $courierId ? $this->couriers($center)->findOrFail($courierId) : null;
        if ($courier) {
            $data['driver_name'] = trim(implode(' ', array_filter([
                $courier->first_name,
                $courier->middle_name,
                $courier->last_name,
            ])));
        }
        if (isset($data['or_cr'])) {
            if ($vehicle->or_cr_path) {
                Storage::disk('public')->delete($vehicle->or_cr_path);
            }
            $data['or_cr_path'] = $data['or_cr']->store('sorting-center/vehicles/or-cr', 'public');
            unset($data['or_cr']);
        }
        $hasCourierField = array_key_exists('courier_registration_id', $request->all());
        if ($hasCourierField) {
            $data['courier_registration_id'] = $courier?->id;
            if (! $courier) {
                $data['driver_name'] = null;
            }
        }
        DB::transaction(function () use ($center, $vehicle, $data, $courier): void {
            $vehicle->update($data);
        });
        return response()->json(['message' => 'Vehicle updated.', 'data' => $vehicle->refresh()]);
    }

    private function vehicleRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        return [
            'name' => [$required, 'string', 'max:150'],
            'type' => [$required, 'string', 'max:80'],
            'plate_number' => [$required, 'string', 'max:50'],
            'make' => ['nullable', 'string', 'max:100'], 'model' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'between:1900,'.(now()->year + 1)],
            'capacity' => ['nullable', 'string', 'max:80'], 'driver_name' => ['nullable', 'string', 'max:150'],
            'registration_date' => ['nullable', 'date'],
            'availability' => ['nullable', Rule::in(['Available', 'In Service', 'Maintenance', 'Out of Service'])],
            'color' => ['nullable', 'string', 'max:60'], 'notes' => ['nullable', 'string', 'max:2000'],
            'or_cr' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function archive(Request $request, string $type, int $record): JsonResponse
    {
        $center = $this->center($request);
        abort_unless(in_array($type, ['vehicles', 'couriers'], true), 404);
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $item = $type === 'vehicles'
            ? $this->vehicles($center)->findOrFail($record)
            : $this->couriers($center)->findOrFail($record);
        $snapshot = $item->toArray();

        $archive = DB::transaction(fn (): SortingCenterFleetArchive => SortingCenterFleetArchive::query()->create([
            'sorting_center_id' => $center->id,
            'record_type' => $type,
            'record_id' => $item->id,
            'reason' => trim($data['reason']),
            'snapshot' => $snapshot,
            'archived_by' => $request->user()->id,
            'archived_at' => now(),
        ]));

        return response()->json(['message' => 'Record archived.', 'data' => [
            'key' => $type.':'.$item->id, 'type' => $type, 'id' => (string) $item->id,
            'reason' => $archive->reason, 'archived_at' => $archive->archived_at, 'snapshot' => $snapshot,
        ]], 201);
    }

    public function restore(Request $request, string $type, int $record): JsonResponse
    {
        $center = $this->center($request);
        abort_unless(in_array($type, ['vehicles', 'couriers'], true), 404);
        $archive = SortingCenterFleetArchive::query()->where('sorting_center_id', $center->id)
            ->where('record_type', $type)->where('record_id', $record)->firstOrFail();
        $archive->delete();
        return response()->json(['message' => 'Record restored.']);
    }
}
