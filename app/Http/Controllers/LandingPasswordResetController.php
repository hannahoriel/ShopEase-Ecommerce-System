<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LandingPasswordResetController extends Controller
{
    private const SESSION_KEY = 'landing_password_reset';

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'portal' => ['required', Rule::in(['main', 'logistics'])],
        ]);

        $email = Str::lower(trim($data['email']));
        $key = 'landing-password-reset:'.hash('sha256', $email);
        if (! RateLimiter::attempt($key, 1, fn () => true, 60)) {
            $seconds = max(1, RateLimiter::availableIn($key));

            return response()->json([
                'message' => 'Please wait before requesting another code.',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', (string) $seconds);
        }

        $roles = $data['portal'] === 'logistics'
            ? [User::ROLE_LOGISTICS, User::ROLE_SORTING_CENTER, User::ROLE_DELIVERY_HUB]
            : [User::ROLE_BUYER, User::ROLE_SELLER, User::ROLE_ADMIN];

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->whereIn('role', $roles)->first();
        $code = (string) random_int(100000, 999999);
        $request->session()->forget(self::SESSION_KEY);
        if ($user) {
            try {
                Mail::send('emails.password-reset-code', ['code' => $code], function ($message) use ($user): void {
                    $message->to($user->email)->subject('ShopEase password reset code');
                });
            } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $exception) {
                report($exception);
                RateLimiter::clear($key);

                return response()->json(['message' => 'We could not send the verification code. Please try again.'], 503);
            }
        }

        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user?->id,
            'email' => $email,
            'role' => $user?->role,
            'fingerprint' => $user ? hash('sha256', $user->getAuthPassword()) : null,
            'code' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        return response()->json(['message' => 'If this email matches an account, we sent a verification code.', 'retry_after' => 60]);
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $state = $this->challenge($request);
        if (! isset($state['code']) || $state['attempts'] >= 5) {
            $this->invalidCode();
        }

        $state['attempts']++;
        $request->session()->put(self::SESSION_KEY, $state);
        if (! Hash::check((string) $data['code'], $state['code']) || ! $state['user_id']) {
            $this->invalidCode();
        }

        unset($state['code']);
        $state['verified'] = true;
        $state['expires_at'] = now()->addMinutes(10)->timestamp;
        $request->session()->put(self::SESSION_KEY, $state);

        return response()->json(['verified' => true]);
    }

    public function reset(Request $request): JsonResponse
    {
        $state = $this->challenge($request);
        if (empty($state['verified'])) {
            $this->invalidCode();
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);
        if (strlen($data['password']) > 72) {
            throw ValidationException::withMessages(['password' => 'Use a password of at most 72 bytes.']);
        }

        DB::transaction(function () use ($state, $data): void {
            $user = User::query()->lockForUpdate()->find($state['user_id']);
            if (! $user || Str::lower($user->email) !== $state['email'] || $user->role !== $state['role']
                || ! hash_equals($state['fingerprint'], hash('sha256', $user->getAuthPassword()))) {
                $this->invalidCode();
            }

            $user->forceFill([
                'password' => $data['password'],
                'remember_token' => Str::random(60),
                'must_change_password' => false,
            ])->save();
            $user->tokens()->delete();
        });

        $request->session()->forget(self::SESSION_KEY);

        return response()->json(['message' => 'Your password was changed successfully.']);
    }

    private function challenge(Request $request): array
    {
        $state = $request->session()->get(self::SESSION_KEY);
        if (! $state || $state['expires_at'] <= now()->timestamp) {
            $this->invalidCode();
        }

        return $state;
    }

    private function invalidCode(): never
    {
        throw ValidationException::withMessages(['code' => 'The code is invalid or expired. Request a new code if needed.']);
    }
}
