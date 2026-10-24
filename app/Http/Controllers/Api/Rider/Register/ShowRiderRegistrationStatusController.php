<?php

namespace App\Http\Controllers\Api\Rider\Register;

use App\Http\Controllers\Controller;
use App\Models\Admin\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderRegistrationStatusController extends Controller
{
    public function __invoke(Request $request, string $token): JsonResponse
    {
        abort_unless(preg_match('/^[A-Za-z0-9]{64}$/', $token) === 1, 404);

        $registration = Registration::query()
            ->where('user_type', 'rider')
            ->where('status_token_hash', hash('sha256', $token))
            ->firstOrFail();

        return response()->json([
            'application_id' => 'RC-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT),
            'status' => $registration->status,
            'reviewed_at' => $registration->reviewed_at?->toISOString(),
            'rejection_reason' => $registration->rejection_reason,
        ]);
    }
}
