<?php

namespace App\Http\Controllers\Api\Rider\Register;

use App\Http\Controllers\Controller;
use App\Http\Services\RegistrationService;
use App\Models\Admin\Registration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreRiderRegistrationController extends Controller
{
    public function __invoke(Request $request, RegistrationService $registration): JsonResponse
    {
        if ($request->input('role') !== User::ROLE_RIDER) {
            throw ValidationException::withMessages([
                'role' => 'This endpoint accepts rider registrations only.',
            ]);
        }
        $user = $registration->register($request);
        $application = Registration::query()
            ->where('user_id', $user->id)
            ->where('user_type', User::ROLE_RIDER)
            ->latest('id')
            ->firstOrFail();
        $statusToken = Str::random(64);
        $application->update(['status_token_hash' => hash('sha256', $statusToken)]);

        return response()->json([
            'message' => 'Rider registration submitted and is awaiting administrator approval.',
            'status' => 'pending',
            'application_id' => 'RC-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
            'status_token' => $statusToken,
        ], 201);
    }
}
