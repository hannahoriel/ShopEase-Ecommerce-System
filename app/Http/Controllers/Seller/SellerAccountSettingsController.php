<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class SellerAccountSettingsController extends Controller
{
    private const PREFERENCES = ['new_orders','order_updates','messages','reviews','compliance','email_alerts','announcements','promotions'];

    private function user(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'seller', 403);
        return $user;
    }

    private function otpKey(int $id, string $purpose): string
    {
        return "seller-security-otp:{$id}:{$purpose}";
    }

    private function tokenKey(int $id, string $purpose): string
    {
        return "seller-security-verified:{$id}:{$purpose}";
    }

    public function sendOtp(Request $request)
    {
        $user = $this->user($request);
        $data = $request->validate(['purpose' => ['required','in:change,forgot']]);
        $purpose = $data['purpose'];
        $limit = "seller-security-send:{$user->id}";
        if (RateLimiter::tooManyAttempts($limit, 5)) {
            return response()->json(['message' => 'Too many code requests. Try again later.'], 429);
        }
        RateLimiter::hit($limit, 600);

        $code = (string) random_int(100000, 999999);
        // Store only a hash. OTP bound to authenticated account and security purpose.
        Cache::put($this->otpKey($user->id, $purpose), [
            'hash' => Hash::make($code), 'attempts' => 0,
        ], now()->addMinutes(10));
        Cache::forget($this->tokenKey($user->id, $purpose));

        try {
            Mail::raw("Your ShopEase seller account verification code is {$code}. It expires in 10 minutes. If you did not request this, ignore this email.", function ($mail) use ($user) {
                $mail->to($user->email)->subject('ShopEase Account Security Verification');
            });
        } catch (\Throwable $exception) {
            Cache::forget($this->otpKey($user->id, $purpose));
            report($exception);
            return response()->json(['message' => 'Email could not be sent. Please check your ShopEase mail configuration.'], 503);
        }
        return response()->json(['message' => 'A verification code was sent to your registered email.']);
    }

    public function verifyOtp(Request $request)
    {
        $user = $this->user($request);
        $data = $request->validate([
            'purpose' => ['required','in:change,forgot'],
            'code' => ['required','digits:6'],
        ]);
        $key = $this->otpKey($user->id, $data['purpose']);
        $record = Cache::get($key);
        if (!$record || ($record['attempts'] ?? 0) >= 5) {
            return response()->json(['message'=>'Code expired or too many attempts. Request a new one.'],422);
        }
        if (!Hash::check((string) $data['code'], $record['hash'])) {
            $record['attempts'] = ($record['attempts'] ?? 0) + 1;
            Cache::put($key, $record, now()->addMinutes(10));
            return response()->json(['message'=>'Invalid verification code.'],422);
        }
        Cache::forget($key);
        $token = Str::random(64);
        Cache::put($this->tokenKey($user->id, $data['purpose']), Hash::make($token), now()->addMinutes(5));
        return response()->json(['message'=>'Email verified.','verification_token'=>$token]);
    }

    private function consumeVerifiedToken($user, string $purpose, string $token): void
    {
        $key = $this->tokenKey($user->id, $purpose);
        $stored = Cache::get($key);
        if (!$stored || !Hash::check($token, $stored)) {
            throw ValidationException::withMessages(['verification_token'=>'Email verification has expired. Please request another code.']);
        }
        Cache::forget($key); // one-time use
    }

    public function changePassword(Request $request)
    {
        $user = $this->user($request);
        $data = $request->validate([
            'current_password'=>['required','string'],
            'new_password'=>['required','confirmed',Password::min(8)],
            'verification_token'=>['required','string'],
        ]);
        if (!Hash::check($data['current_password'],$user->password)) {
            throw ValidationException::withMessages(['current_password'=>'Current password is incorrect.']);
        }
        $this->consumeVerifiedToken($user,'change',$data['verification_token']);
        $user->forceFill(['password'=>Hash::make($data['new_password'])])->save();
        return response()->json(['message'=>'Password changed successfully.']);
    }

    public function resetPassword(Request $request)
    {
        $user = $this->user($request);
        $data = $request->validate([
            'new_password'=>['required','confirmed',Password::min(8)],
            'verification_token'=>['required','string'],
        ]);
        $this->consumeVerifiedToken($user,'forgot',$data['verification_token']);
        $user->forceFill(['password'=>Hash::make($data['new_password'])])->save();
        return response()->json(['message'=>'Password reset successfully.']);
    }

    public function saveNotifications(Request $request)
    {
        $user = $this->user($request);
        $data=$request->validate(['preferences'=>['required','array']]);
        $preferences=[];
        foreach(self::PREFERENCES as $key) {
            if(!array_key_exists($key,$data['preferences']) || !is_bool($data['preferences'][$key])) {
                throw ValidationException::withMessages(['preferences'=>'Invalid notification preferences.']);
            }
            $preferences[$key]=$data['preferences'][$key];
        }
        DB::table('seller_notification_preferences')->updateOrInsert(
            ['user_id'=>$user->id],
            ['preferences'=>json_encode($preferences),'updated_at'=>now()]
        );
        return response()->json(['message'=>'Notification preferences saved.']);
    }
}
