<?php

namespace App\Http\Controllers\Api\Rider\Register;

use App\Http\Controllers\Controller;
use App\Http\Services\RegistrationService;
use App\Http\Services\DeliveryHubRiderRegistrationService;
use App\Models\Admin\Registration;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreRiderRegistrationController extends Controller
{
    public function __invoke(
        Request $request,
        RegistrationService $registration,
        DeliveryHubRiderRegistrationService $hubRegistrations,
    ): JsonResponse
    {
        if ($request->input('role') !== User::ROLE_RIDER) {
            throw ValidationException::withMessages([
                'role' => 'This endpoint accepts rider registrations only.',
            ]);
        }
        $isCourier = $request->input('registration_type') === 'courier';
        $sortingCenter = null;
        if ($isCourier) {
            $centerId = $request->input('sorting_center_id');
            $logisticsId = $request->input('logistics_id');
            $sortingCenter = is_numeric($centerId)
                ? LogisticsBranch::query()
                    ->whereKey((int) $centerId)
                    ->where('logistics_id', $logisticsId)
                    ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim((string) $request->input('assignment_province')))])
                    ->where('status', 'active')
                    ->whereNull('archived_at')
                    ->whereHas('logistics', fn ($query) => $query
                        ->where('registration_status', 'active')
                        ->whereHas('user', fn ($user) => $user->where('role', User::ROLE_LOGISTICS)->where('registration_status', 'active')))
                    ->whereHas('user', fn ($query) => $query
                        ->where('role', User::ROLE_SORTING_CENTER)
                        ->where('registration_status', 'active'))
                    ->first()
                : null;

            if (! $sortingCenter) {
                throw ValidationException::withMessages([
                    'sorting_center_id' => 'Choose an active sorting center for this courier registration.',
                ]);
            }
        }
        $hub = $isCourier ? null : $hubRegistrations->resolveHub($request);
        [$user, $application, $statusToken] = DB::transaction(function () use ($request, $registration, $hubRegistrations, $hub, $isCourier, $sortingCenter): array {
            $user = $registration->register($request);
            if ($hub) {
                $hubRegistrations->submit($user, $hub);
            }
            if ($isCourier && $sortingCenter) {
                $rider = $user->riderProfile()->firstOrFail();
                $courierData = [
                    'sorting_center_id' => $sortingCenter->id,
                    'user_id' => $user->id,
                    'first_name' => $rider->first_name,
                    'middle_name' => $rider->middle_initial,
                    'last_name' => $rider->last_name,
                    'sex' => $rider->sex,
                    'birthday' => $rider->birthday,
                    'email' => $user->email,
                    'phone' => $rider->contact_no,
                    'region' => $rider->assignment_region,
                    'province' => $rider->assignment_province,
                    'city' => $rider->municipality,
                    'barangay' => $rider->barangay,
                    'street' => $rider->street,
                    'house_no' => $rider->house_number,
                    'vehicle' => $rider->vehicle,
                    'plate_number' => $rider->plate_number,
                    'or_cr_path' => $rider->upload_or_cr,
                    'drivers_license_path' => $rider->upload_id_license,
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ];
                $courierApplication = CourierRegistration::query()
                    ->where('user_id', $user->id)
                    ->latest('id')
                    ->first();
                if ($courierApplication) {
                    $courierApplication->update($courierData);
                } else {
                    CourierRegistration::query()->create($courierData);
                }
            }
            $application = Registration::query()
                ->where('user_id', $user->id)
                ->where('user_type', User::ROLE_RIDER)
                ->latest('id')
                ->firstOrFail();
            $statusToken = Str::random(64);
            $application->update(['status_token_hash' => hash('sha256', $statusToken)]);

            return [$user, $application, $statusToken];
        });

        return response()->json([
            'message' => $isCourier
                ? 'Courier registration submitted and is awaiting sorting center approval.'
                : 'Rider registration submitted and is awaiting delivery hub approval.',
            'status' => 'pending',
            'application_id' => 'RC-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
            'status_token' => $statusToken,
        ], 201);
    }
}
