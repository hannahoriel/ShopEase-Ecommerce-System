<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Services\RegistrationService;
use App\Http\Services\DeliveryHubRiderRegistrationService;
use App\Models\Admin\Registration;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if ($user->role !== User::ROLE_ADMIN && $user->registration_status === 'rejected') {
            throw ValidationException::withMessages([
                'email' => 'Your registration was not approved. Contact the delivery hub if you need help.',
            ]);
        }

        if ($user->role !== User::ROLE_ADMIN && $user->registration_status === 'pending') {
            throw ValidationException::withMessages([
                'email' => $user->role === User::ROLE_RIDER
                    ? 'Your rider registration is still pending delivery hub approval.'
                    : 'Your registration is still pending administrator approval.',
            ]);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'role' => $user->role,
            'rider_type' => $user->role === User::ROLE_RIDER
                ? ($user->riderProfile?->registration_type ?: 'rider')
                : null,
            'token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }

    public function register(Request $request, DeliveryHubRiderRegistrationService $hubRegistrations): JsonResponse
    {
        $role = $request->input('role', User::ROLE_BUYER);
        $isCourier = $role === User::ROLE_RIDER
            && $request->input('registration_type') === 'courier';
        $center = null;
        $deliveryHub = null;
        if ($isCourier) {
            $centerData = $request->validate([
                'sorting_center_id' => ['required', 'integer', 'exists:logistics_branches,id'],
                'assignment_province' => ['required', 'string', 'max:255'],
            ]);
            $center = LogisticsBranch::query()->whereKey($centerData['sorting_center_id'])
                ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim($centerData['assignment_province']))])
                ->where('status', 'active')->whereNull('archived_at')
                ->whereHas('user', fn ($query) => $query
                    ->where('role', User::ROLE_SORTING_CENTER)
                    ->where('registration_status', 'active'))
                ->first();
            if (! $center) {
                throw ValidationException::withMessages([
                    'sorting_center_id' => 'Choose an active sorting center for your assignment province.',
                ]);
            }
        }
        if ($role === User::ROLE_RIDER && ! $isCourier) {
            $deliveryHub = $hubRegistrations->resolveHub($request);
        }
        $verification = null;
        if (in_array($role, [User::ROLE_BUYER, User::ROLE_SELLER, User::ROLE_RIDER], true)) {
            $verification = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'email_verification_token' => ['required', 'string'],
            ]);
            MobileRegistrationVerificationController::assertVerifiedEmail(
                $verification['email'],
                $role,
                $verification['email_verification_token'],
            );
        }
        [$user, $application, $statusToken] = DB::transaction(function () use ($request, $role, $isCourier, $center, $deliveryHub, $hubRegistrations): array {
            $user = $this->registrationService->register($request);

            if ($isCourier) {
                $rider = $user->riderProfile()->firstOrFail();
                CourierRegistration::query()->create([
                    'sorting_center_id' => $center->id,
                    'user_id' => $user->id,
                    'first_name' => $rider->first_name,
                    'middle_name' => $rider->middle_initial,
                    'last_name' => $rider->last_name,
                    'sex' => $rider->sex,
                    'birthday' => $rider->birthday,
                    'email' => $user->email,
                    'phone' => $rider->contact_no,
                    'region' => $rider->assignment_region,
                    'province' => $rider->province,
                    'city' => $rider->municipality,
                    'barangay' => $rider->barangay,
                    'street' => $rider->street,
                    'house_no' => $rider->house_number,
                    'vehicle' => $rider->vehicle,
                    'plate_number' => $rider->plate_number,
                    'or_cr_path' => $rider->upload_or_cr,
                    'drivers_license_path' => $rider->upload_id_license,
                    'status' => 'pending',
                ]);
            }

            if ($deliveryHub) {
                $hubRegistrations->submit($user, $deliveryHub);
            }

            $application = Registration::query()
                ->where('user_id', $user->id)
                ->where('user_type', $role)
                ->latest('id')
                ->first();
            $statusToken = null;
            if ($role === User::ROLE_RIDER && $application) {
                $statusToken = Str::random(64);
                $application->update(['status_token_hash' => hash('sha256', $statusToken)]);
            }

            return [$user, $application, $statusToken];
        });
        if ($verification !== null) {
            MobileRegistrationVerificationController::consumeVerifiedEmail(
                $verification['email'],
                $role,
            );
        }

        return response()->json([
            'message' => 'Your registration has been submitted and is awaiting administrator approval.',
            'status' => 'pending',
            'application_id' => $role === User::ROLE_RIDER && $application
                ? 'RC-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT)
                : null,
            'status_token' => $statusToken,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
}
