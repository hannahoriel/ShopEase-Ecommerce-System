<?php

namespace App\Http\Controllers\Api\Rider\Register;

use App\Http\Controllers\Controller;
use App\Http\Services\RegistrationService;
use App\Http\Services\DeliveryHubRiderRegistrationService;
use App\Models\Admin\Registration;
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
        $hub = $isCourier ? null : $hubRegistrations->resolveHub($request);
        [$user, $application, $statusToken] = DB::transaction(function () use ($request, $registration, $hubRegistrations, $hub): array {
            $user = $registration->register($request);
            if ($hub) {
                $hubRegistrations->submit($user, $hub);
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
            'message' => 'Rider registration submitted and is awaiting delivery hub approval.',
            'status' => 'pending',
            'application_id' => 'RC-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
            'status_token' => $statusToken,
        ], 201);
    }
}
