<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {}

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        return $this->authenticate($request, [User::ROLE_BUYER, User::ROLE_SELLER]);
    }

    public function loginLogistics(Request $request): RedirectResponse
    {
        return $this->authenticate($request, [
            User::ROLE_LOGISTICS,
            User::ROLE_SORTING_CENTER,
            User::ROLE_DELIVERY_HUB,
        ]);
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->authenticate($request, [User::ROLE_ADMIN]);
    }

    private function authenticate(Request $request, array $allowedRoles): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember', true))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if (! in_array($user->role, $allowedRoles, true)) {
            Auth::logout();

            $accountType = in_array(User::ROLE_LOGISTICS, $allowedRoles, true)
                ? 'a logistics, sorting center, or delivery hub account'
                : (in_array(User::ROLE_ADMIN, $allowedRoles, true) ? 'an administrator account' : 'a buyer or seller account');

            throw ValidationException::withMessages([
                'email' => 'These credentials are not for '.$accountType.'.',
            ]);
        }

        $registrationStatus = $user->registration_status;

        if ($user->role !== User::ROLE_ADMIN
            && in_array($registrationStatus, ['pending', 'rejected'], true)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => $registrationStatus === 'rejected'
                    ? 'Your registration was rejected and this account cannot log in.'
                    : 'Your registration is still pending administrator approval.',
            ]);
        }

        $request->session()->regenerate();

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
