<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminAccountSecurityController extends Controller
{
    private function admin(Request $request): User
    {
        $user = $request->user();
        abort_unless($user && $user->role === User::ROLE_ADMIN, 403);

        return $user;
    }

    private function otpKey(int $userId, string $purpose): string
    {
        return "admin-security-otp:{$userId}:{$purpose}";
    }

    private function verifiedKey(int $userId, string $purpose): string
    {
        return "admin-security-verified:{$userId}:{$purpose}";
    }

    private function initialSentKey(int $userId, string $purpose): string
    {
        return "admin-security-initial-sent:{$userId}:{$purpose}";
    }

    public function sendOtp(Request $request)
    {
        $admin = $this->admin($request);
        $data = $request->validate([
            'purpose' => ['required', 'in:change,forgot'],
            'current_password' => ['required_if:purpose,change', 'nullable', 'string'],
        ]);
        $purpose = $data['purpose'];
        if ($purpose === 'change' && ! Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }
        $resendLimit = "admin-security-resend:{$admin->id}:{$purpose}";
        $isResend = Cache::has($this->initialSentKey($admin->id, $purpose));

        if ($isResend && RateLimiter::tooManyAttempts($resendLimit, 1)) {
            $retryAfter = RateLimiter::availableIn($resendLimit);

            return response()->json([
                'message' => 'Please wait before requesting another verification code.',
                'retry_after' => $retryAfter,
            ], 429)->header('Retry-After', (string) $retryAfter);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->otpKey($admin->id, $purpose), [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));
        Cache::forget($this->verifiedKey($admin->id, $purpose));

        try {
            Mail::raw(
                "Your ShopEase admin account verification code is {$code}. It expires in 10 minutes. If you did not request this, ignore this email.",
                fn ($mail) => $mail->to($admin->email)->subject('ShopEase Admin Account Security Verification'),
            );
        } catch (\Throwable $exception) {
            Cache::forget($this->otpKey($admin->id, $purpose));
            report($exception);

            return response()->json([
                'message' => 'Email could not be sent. Please check your ShopEase mail configuration.',
            ], 503);
        }

        if ($isResend) {
            RateLimiter::hit($resendLimit, 600);
        } else {
            Cache::put($this->initialSentKey($admin->id, $purpose), true, now()->addDays(30));
        }

        return response()->json(['message' => 'A verification code was sent to your registered email.']);
    }

    public function verifyOtp(Request $request)
    {
        $admin = $this->admin($request);
        $data = $request->validate([
            'purpose' => ['required', 'in:change,forgot'],
            'code' => ['required', 'digits:6'],
        ]);
        $key = $this->otpKey($admin->id, $data['purpose']);
        $record = Cache::get($key);

        if (! $record || ($record['attempts'] ?? 0) >= 5) {
            return response()->json([
                'message' => 'Code expired or too many attempts. Request a new one.',
            ], 422);
        }

        if (! Hash::check((string) $data['code'], $record['hash'])) {
            $record['attempts']++;
            Cache::put($key, $record, now()->addMinutes(10));

            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        Cache::forget($key);
        $token = Str::random(64);
        Cache::put($this->verifiedKey($admin->id, $data['purpose']), Hash::make($token), now()->addMinutes(5));

        return response()->json([
            'message' => 'Email verified.',
            'verification_token' => $token,
        ]);
    }

    private function consumeVerifiedToken(User $admin, string $purpose, string $token): void
    {
        $key = $this->verifiedKey($admin->id, $purpose);
        $stored = Cache::get($key);
        if (! $stored || ! Hash::check($token, $stored)) {
            throw ValidationException::withMessages([
                'verification_token' => 'Email verification has expired. Please request another code.',
            ]);
        }

        Cache::forget($key);
    }

    public function changePassword(Request $request)
    {
        $admin = $this->admin($request);
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
            'verification_token' => ['required', 'string'],
        ]);

        if (! Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $this->consumeVerifiedToken($admin, 'change', $data['verification_token']);
        $admin->forceFill(['password' => Hash::make($data['new_password'])])->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function resetPassword(Request $request)
    {
        $admin = $this->admin($request);
        $data = $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
            'verification_token' => ['required', 'string'],
        ]);

        $this->consumeVerifiedToken($admin, 'forgot', $data['verification_token']);
        $admin->forceFill(['password' => Hash::make($data['new_password'])])->save();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
