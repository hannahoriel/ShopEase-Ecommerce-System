<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {}

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        return $this->authenticate($request, [User::ROLE_BUYER, User::ROLE_SELLER, User::ROLE_ADMIN]);
    }

    public function loginLogistics(Request $request): RedirectResponse|JsonResponse
    {
        return $this->authenticate($request, [
            User::ROLE_LOGISTICS,
            User::ROLE_SORTING_CENTER,
            User::ROLE_DELIVERY_HUB,
        ]);
    }

    private function authenticate(Request $request, array $allowedRoles): RedirectResponse|JsonResponse
    {
        $request->session()->forget('first_password');
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! in_array($user->role, $allowedRoles, true)) {
            $accountType = in_array(User::ROLE_LOGISTICS, $allowedRoles, true)
                ? 'a logistics, sorting center, or delivery hub account'
                : 'a buyer, seller, or administrator account';

            throw ValidationException::withMessages([
                'email' => 'These credentials are not for '.$accountType.'.',
            ]);
        }

        $registrationStatus = $user->registration_status;

        if ($user->role !== User::ROLE_ADMIN
            && in_array($registrationStatus, ['pending', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'email' => $registrationStatus === 'rejected'
                    ? 'Your registration was rejected and this account cannot log in.'
                    : 'Your registration is still pending administrator approval.',
            ]);
        }

        $remember = $request->boolean('remember', true);
        if ($user->must_change_password) {
            $request->session()->regenerate();
            $request->session()->put('first_password', [
                'user_id' => $user->id,
                'fingerprint' => hash('sha256', $user->getAuthPassword()),
                'remember' => $remember,
                'expires_at' => now()->addMinutes(15)->timestamp,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['password_setup_required' => true, 'csrf_token' => $request->session()->token()]);
            }

            return redirect()->route('login', ['modal' => 1, 'setup' => 1]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->dashboardRedirect($user);
    }

    public function setFirstPassword(Request $request): RedirectResponse
    {
        $challenge = $request->session()->get('first_password');
        if (! $challenge || $challenge['expires_at'] <= now()->timestamp) {
            $request->session()->forget('first_password');
            throw ValidationException::withMessages(['password' => 'Your session expired. Log in again with your emailed password.']);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = DB::transaction(function () use ($challenge, $data): User {
            $user = User::query()->lockForUpdate()->find($challenge['user_id']);
            if (! $user?->must_change_password
                || ! hash_equals($challenge['fingerprint'], hash('sha256', $user->getAuthPassword()))
                || in_array($user->registration_status, ['pending', 'rejected'], true)) {
                throw ValidationException::withMessages(['password' => 'Log in again to continue.']);
            }
            if (strlen($data['password']) > 72 || Hash::check($data['password'], $user->getAuthPassword())) {
                throw ValidationException::withMessages(['password' => 'Use a new password of at most 72 bytes, different from the emailed password.']);
            }

            $user->forceFill([
                'password' => $data['password'],
                'must_change_password' => false,
                'remember_token' => Str::random(60),
            ])->save();

            return $user;
        });

        $request->session()->forget('first_password');
        Auth::login($user, $challenge['remember']);
        $request->session()->regenerate();

        return $this->dashboardRedirect($user);
    }

    private function dashboardRedirect(User $user): RedirectResponse
    {
        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_BUYER => redirect()->route('buyer.dashboard'),
            User::ROLE_SELLER => redirect()->route('seller.dashboard'),
            User::ROLE_LOGISTICS => redirect()->route('logistics.dashboard'),
            User::ROLE_SORTING_CENTER => redirect()->route('sorting-center.dashboard'),
            User::ROLE_DELIVERY_HUB => redirect()->route('delivery-hub.dashboard'),
            User::ROLE_RIDER => redirect()->route('rider.dashboard'),
            User::ROLE_COURIER => redirect()->route('courier.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    /**
     * Register a new user and hold the account pending admin approval.
     */
    public function register(Request $request): RedirectResponse
    {
        $this->registrationService->register($request);

        return redirect()
            ->route('login')
            ->with('status', 'Your registration has been submitted and is awaiting administrator approval.');
    }

    /**
     * Complete a buyer registration collected by the multi-step form.
     */
    public function completeBuyerRegistration(array $data): void
    {
        $this->registrationService->completeBuyerRegistration($data);
    }

    /**
     * Complete a seller registration collected by the multi-step form.
     */
    public function completeSellerRegistration(array $data): void
    {
        $this->registrationService->completeSellerRegistration($data);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.page');
    }
}
