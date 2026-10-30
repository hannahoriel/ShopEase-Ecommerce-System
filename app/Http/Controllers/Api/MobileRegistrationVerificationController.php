<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BuyerVerificationCode;
use App\Mail\SellerVerificationCode;
use App\Mail\RiderVerificationCode;
use App\Models\Admin\Registration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileRegistrationVerificationController extends Controller
{
    private function scope(string $email, string $role): string
    {
        return 'mobile-registration:'.$role.':'.hash('sha256', mb_strtolower(trim($email)));
    }

    public function sendCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:buyer,seller,rider'],
        ]);
        $email = mb_strtolower(trim($data['email']));
        $role = $data['role'];
        $scope = $this->scope($email, $role);

        $hasRejectedRegistration = Registration::query()
            ->where('email', $email)
            ->where('status', 'rejected')
            ->exists();
        if (
            User::query()->where('email', $email)
                ->where('registration_status', '!=', 'rejected')
                ->exists()
            && ! $hasRejectedRegistration
        ) {
            throw ValidationException::withMessages([
                'email' => 'This email address is already registered. Please use another email address or log in.',
            ]);
        }

        $cooldownKey = "{$scope}:cooldown";
        if (! Cache::add($cooldownKey, true, now()->addSeconds(120))) {
            return response()->json([
                'message' => 'Please wait before requesting another verification code.',
            ], 429);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put("{$scope}:otp", [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));
        Cache::forget("{$scope}:verified");

        try {
            $mail = match ($role) {
                'buyer' => new BuyerVerificationCode($code),
                'seller' => new SellerVerificationCode($code),
                default => new RiderVerificationCode($code),
            };
            Mail::to($email)->send($mail);
        } catch (\Throwable $exception) {
            Cache::forget("{$scope}:otp");
            Cache::forget($cooldownKey);
            report($exception);

            return response()->json([
                'message' => 'We could not send the verification email. Check the server mail configuration and try again.',
            ], 503);
        }

        return response()->json(['message' => 'A verification code has been sent to your email address.']);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:buyer,seller,rider'],
            'code' => ['required', 'digits:6'],
        ]);
        $scope = $this->scope($data['email'], $data['role']);
        $otpKey = "{$scope}:otp";
        $record = Cache::get($otpKey);

        if (! is_array($record) || ($record['attempts'] ?? 0) >= 5) {
            return response()->json([
                'message' => 'Code expired or too many attempts. Request a new one.',
            ], 422);
        }

        if (! Hash::check($data['code'], $record['hash'] ?? '')) {
            $record['attempts'] = ($record['attempts'] ?? 0) + 1;
            Cache::put($otpKey, $record, now()->addMinutes(10));

            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        Cache::forget($otpKey);
        $token = Str::random(64);
        Cache::put("{$scope}:verified", Hash::make($token), now()->addMinutes(10));

        return response()->json([
            'message' => 'Email verified.',
            'verification_token' => $token,
        ]);
    }

    public static function assertVerifiedEmail(string $email, string $role, string $token): void
    {
        $scope = 'mobile-registration:'.$role.':'.hash('sha256', mb_strtolower(trim($email)));
        $key = "{$scope}:verified";
        $stored = Cache::get($key);

        if (! is_string($stored) || ! Hash::check($token, $stored)) {
            throw ValidationException::withMessages([
                'email' => 'Verify your email before submitting this registration.',
            ]);
        }
    }

    public static function consumeVerifiedEmail(string $email, string $role): void
    {
        $scope = 'mobile-registration:'.$role.':'.hash('sha256', mb_strtolower(trim($email)));
        $key = "{$scope}:verified";
        Cache::forget($key);
    }
}
