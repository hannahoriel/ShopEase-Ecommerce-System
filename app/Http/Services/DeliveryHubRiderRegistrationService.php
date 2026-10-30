<?php

namespace App\Http\Services;

use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeliveryHubRiderRegistrationService
{
    public function resolveHub(Request $request): LogisticsDeliveryHub
    {
        $data = $request->validate([
            'delivery_hub_id' => ['nullable', 'integer'],
        ]);
        $province = trim((string) ($request->input('assignment_province') ?: $request->input('province')));
        $query = LogisticsDeliveryHub::query()
            ->where('status', 'active')
            ->whereNull('archived_at')
            ->whereRaw('LOWER(province) = ?', [mb_strtolower($province)])
            ->whereHas('logisticsBranch', fn ($branch) => $branch
                ->where('status', 'active')->whereNull('archived_at')
                ->whereHas('user', fn ($user) => $user
                    ->where('role', User::ROLE_SORTING_CENTER)
                    ->where('registration_status', 'active')))
            ->whereHas('user', fn ($user) => $user
                ->where('role', User::ROLE_DELIVERY_HUB)
                ->where('registration_status', 'active'));

        if (! empty($data['delivery_hub_id'])) {
            $hub = (clone $query)->whereKey($data['delivery_hub_id'])->first();
            if (! $hub) {
                throw ValidationException::withMessages([
                    'delivery_hub_id' => 'Choose an active delivery hub in your province.',
                ]);
            }

            return $hub;
        }

        $preferredHub = trim((string) $request->input('municipal_hub'));
        if ($preferredHub !== '') {
            $query->where(fn ($hub) => $hub
                ->whereRaw('LOWER(hub_name) = ?', [mb_strtolower($preferredHub)])
                ->orWhereRaw('LOWER(municipality) = ?', [mb_strtolower($preferredHub)]));
        }

        $hubs = $query->orderBy('hub_name')->limit(2)->get();
        if ($hubs->count() !== 1) {
            throw ValidationException::withMessages([
                'delivery_hub_id' => $hubs->isEmpty()
                    ? 'No active delivery hub is available in your province. Please contact support.'
                    : 'Select the delivery hub you want to register with.',
            ]);
        }

        return $hubs->first();
    }

    public function submit(User $user, LogisticsDeliveryHub $hub): DeliveryHubRiderRegistration
    {
        return DeliveryHubRiderRegistration::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'logistics_delivery_hub_id' => $hub->id,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ],
        );
    }

    public function present(DeliveryHubRiderRegistration $application): array
    {
        $application->loadMissing(['user.riderProfile', 'deliveryHub.logisticsBranch.logistics']);
        $user = $application->user;
        $rider = $user?->riderProfile;
        $hub = $application->deliveryHub;

        return [
            'id' => $application->id,
            'user_id' => $user?->id,
            'delivery_hub_id' => $hub?->id,
            'delivery_hub_name' => $hub?->hub_name,
            'logistics_company_name' => $hub?->logisticsBranch?->logistics?->business_name,
            'first_name' => $rider?->first_name,
            'middle_name' => $rider?->middle_initial,
            'last_name' => $rider?->last_name,
            'full_name' => trim(implode(' ', array_filter([$rider?->first_name, $rider?->middle_initial, $rider?->last_name]))),
            'sex' => $rider?->sex,
            'birthday' => $rider?->birthday?->toDateString(),
            'age' => $rider?->age,
            'email' => $user?->email,
            'phone' => $rider?->contact_no,
            'contact_no' => $rider?->contact_no,
            'region' => $rider?->assignment_region,
            'province' => $rider?->province,
            'preferred_province' => $hub?->province,
            'municipality' => $rider?->municipality,
            'city' => $rider?->municipality,
            'preferred_municipality' => $hub?->municipality,
            'barangay' => $rider?->barangay,
            'street' => $rider?->street,
            'house_no' => $rider?->house_number,
            'postal_code' => $hub?->postal_code,
            'vehicle_type' => $rider?->vehicle,
            'vehicle' => $rider?->vehicle,
            'plate_number' => $rider?->plate_number,
            'or_cr' => $rider?->upload_or_cr ? Storage::disk('public')->url($rider->upload_or_cr) : null,
            'drivers_license' => $rider?->upload_id_license ? Storage::disk('public')->url($rider->upload_id_license) : null,
            'status' => $application->status,
            'rejection_reason' => $application->rejection_reason,
            'created_at' => $application->created_at?->toISOString(),
            'reviewed_at' => $application->reviewed_at?->toISOString(),
        ];
    }
}
