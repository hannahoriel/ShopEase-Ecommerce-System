<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        if ($user->role !== User::ROLE_ADMIN && $user->registration_status === 'pending') {
            throw ValidationException::withMessages([
                'email' => 'Your registration is still pending administrator approval.',
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

    public function register(Request $request): JsonResponse
    {
        $role = $request->input('role', User::ROLE_BUYER);
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
        $this->registrationService->register($request);
        if ($verification !== null) {
            MobileRegistrationVerificationController::consumeVerifiedEmail(
                $verification['email'],
                $role,
            );
        }

        return response()->json([
            'message' => 'Your registration has been submitted and is awaiting administrator approval.',
            'status' => 'pending',
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
