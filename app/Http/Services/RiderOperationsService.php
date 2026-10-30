<?php

namespace App\Http\Services;

use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RiderOperationsService
{
    public function rider(User $user): Rider
    {
        return Rider::query()
            ->with(['user', 'logisticsBranch.logistics'])
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
    }

    public function profile(Rider $rider): array
    {
        return [
            'id' => $rider->id,
            'user_id' => $rider->user_id,
            'name' => $rider->user->name,
            'email' => $rider->user->email,
            'phone' => $rider->contact_no,
            'first_name' => $rider->first_name,
            'last_name' => $rider->last_name,
            'rider_id' => (string) $rider->id,
            'registration_type' => $rider->registration_type ?: 'rider',
            'assignment_region' => $rider->assignment_region,
            'assignment_province' => $rider->assignment_province,
            'courier' => $rider->courier_company ?: $rider->logisticsBranch?->logistics?->business_name,
            'branch' => $rider->logisticsBranch?->name,
            'branch_address' => $rider->logisticsBranch?->address,
            'branch_contact_person' => $rider->logisticsBranch?->contact_person,
            'branch_phone' => $rider->logisticsBranch?->phone,
            'logistics_operation' => $rider->logistics_operation,
            'municipal_hub' => $rider->municipal_hub,
            'barangay' => $rider->barangay,
            'address' => collect([
                $rider->house_number,
                $rider->street,
                $rider->barangay,
                $rider->municipality,
                $rider->province,
            ])->filter()->implode(', '),
            'vehicle' => $rider->vehicle,
            'plate_number' => $rider->plate_number,
            'profile_photo_url' => $rider->user->profile_photo_path
                ? Storage::disk('public')->url($rider->user->profile_photo_path)
                : null,
        ];
    }

    public function storePrivatePhoto(string $directory, \Illuminate\Http\UploadedFile $photo): string
    {
        $path = Storage::disk('local')->putFile($directory, $photo);
        if (! $path) {
            throw new \RuntimeException('The uploaded photo could not be stored.');
        }

        return $path;
    }
}
