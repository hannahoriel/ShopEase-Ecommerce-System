<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobilePasswordSecurityController extends Controller
{
    private const ALLOWED_ROLES = [
        User::ROLE_BUYER,
        User::ROLE_SELLER,
        User::ROLE_RIDER,
    ];

    private function user(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && in_array($user->role, self::ALLOWED_ROLES, true), 403);

        return $user;
    }

    private function emailKey(string $email): string
    {
        return hash('sha256', mb_strtolower(trim($email)));
    }

    private function otpKey(string $scope): string
    {
        return "mobile-password:{$scope}:otp";
    }

    private function tokenKey(string $scope): string
    {
        return "mobile-password:{$scope}:verified";
    }

    private function sendCode(User $user, string $scope): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put($this->otpKey($scope), [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));
        Cache::forget($this->tokenKey($scope));

        Mail::raw(
            "Your ShopEase password verification code is {$code}. It expires in 10 minutes. If you did not request this, ignore this email.",
            fn ($mail) => $mail->to($user->email)->subject('ShopEase Password Verification')
        );
    }

    private function verifyCode(Request $request, string $scope): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $key = $this->otpKey($scope);
        $record = Cache::get($key);

        if (! is_array($record) || ($record['attempts'] ?? 0) >= 5) {
            return response()->json([
                'message' => 'Code expired or too many attempts. Request a new one.',
            ], 422);
        }

        if (! Hash::check((string) $data['code'], $record['hash'] ?? '')) {
            $record['attempts'] = ($record['attempts'] ?? 0) + 1;
            Cache::put($key, $record, now()->addMinutes(10));

            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        Cache::forget($key);
        $token = Str::random(64);
        Cache::put($this->tokenKey($scope), Hash::make($token), now()->addMinutes(5));

        return response()->json([
            'message' => 'Email verified.',
            'verification_token' => $token,
        ]);
    }

    private function consumeToken(string $scope, string $token): void
    {
        $key = $this->tokenKey($scope);
        $stored = Cache::get($key);
        if (! is_string($stored) || ! Hash::check($token, $stored)) {
            throw ValidationException::withMessages([
                'verification_token' => 'Email verification has expired. Please request another code.',
            ]);
        }

        Cache::forget($key);
    }

    public function sendForgotOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $email = mb_strtolower(trim($data['email']));
        $scope = 'forgot:'.$this->emailKey($email);
        $cooldownKey = "mobile-password:forgot:cooldown:{$this->emailKey($email)}";

        if (! Cache::add($cooldownKey, true, now()->addSeconds(60))) {
            return response()->json([
                'message' => 'Please wait before requesting another verification code.',
            ], 429);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('role', self::ALLOWED_ROLES)
            ->first();

        if ($user) {
            try {
                $this->sendCode($user, $scope);
            } catch (\Throwable $exception) {
                Cache::forget($this->otpKey($scope));
                report($exception);

                if (app()->environment('local')) {
                    return response()->json([
                        'message' => 'Email delivery failed. Check the local mail transport configuration.',
                    ], 503);
                }
            }
        } elseif (app()->environment('local')) {
            return response()->json([
                'message' => 'No buyer, seller, or rider account uses that email address.',
            ], 404);
        }

        return response()->json([
            'message' => 'If an eligible ShopEase account uses that email, a verification code has been sent.',
        ]);
    }

    public function verifyForgotOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $scope = 'forgot:'.$this->emailKey($data['email']);

        return $this->verifyCode($request, $scope);
    }

    public function resetForgottenPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'verification_token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $email = mb_strtolower(trim($data['email']));
        $scope = 'forgot:'.$this->emailKey($email);
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('role', self::ALLOWED_ROLES)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => 'Account could not be verified.']);
        }

        $this->consumeToken($scope, $data['verification_token']);
        $user->forceFill(['password' => Hash::make($data['password'])])->save();
        $user->tokens()->delete();

        return response()->json(['message' => 'Password reset successfully. Please log in again.']);
    }

    public function sendChangeOtp(Request $request): JsonResponse
    {
        $user = $this->user($request);
        $data = $request->validate(['current_password' => ['required', 'string']]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $scope = 'change:'.$user->id;
        $cooldownKey = "mobile-password:change:cooldown:{$user->id}";
        if (! Cache::add($cooldownKey, true, now()->addSeconds(60))) {
            return response()->json([
                'message' => 'Please wait before requesting another verification code.',
            ], 429);
        }

        try {
            $this->sendCode($user, $scope);
        } catch (\Throwable $exception) {
            Cache::forget($this->otpKey($scope));
            Cache::forget($cooldownKey);
            report($exception);

            return response()->json([
                'message' => 'Email could not be sent. Please check your ShopEase mail configuration.',
            ], 503);
        }

        return response()->json(['message' => 'A verification code was sent to your registered email.']);
    }

    public function verifyChangeOtp(Request $request): JsonResponse
    {
        $user = $this->user($request);

        return $this->verifyCode($request, 'change:'.$user->id);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = $this->user($request);
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'verification_token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $this->consumeToken('change:'.$user->id, $data['verification_token']);
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
