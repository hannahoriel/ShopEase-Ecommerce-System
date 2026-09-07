<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Logistics;
use App\Models\Rider;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Login successful.',
                'token' => $request->user()->createToken('api')->plainTextToken,
                'user' => $request->user(),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Register a new user and hold the account pending admin approval.
     */
    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $role = $request->input('role', User::ROLE_BUYER);

        $rules = [
            'role' => ['required', 'in:' . implode(',', [
                User::ROLE_BUYER,
                User::ROLE_SELLER,
                User::ROLE_RIDER,
            ])],
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'sex' => ['required', 'in:male,female,other'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_no' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'province' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'upload_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'upload_id_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
        ];

        if ($role === User::ROLE_SELLER) {
            $rules['business_name'] = ['required', 'string', 'max:255'];
            $rules['line_of_business'] = ['required', 'string', 'max:255'];
            $rules['upload_business_permit'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'];
        }

        if ($role === User::ROLE_RIDER) {
            $rules['vehicle'] = ['required', 'string', 'max:255'];
            $rules['plate_number'] = ['required', 'string', 'max:50'];
            $rules['upload_or_cr'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'];
        }

        $validated = $request->validate($rules);

        $birthday = $request->date('birthday');

        $user = User::create([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'sex' => $validated['sex'],
            'email' => $validated['email'],
            'contact_no' => $validated['contact_no'],
            'birthday' => $validated['birthday'],
            'age' => $this->calculateAge($birthday),
            'province' => $validated['province'],
            'municipality' => $validated['municipality'],
            'barangay' => $validated['barangay'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'registration_status' => 'pending',
        ]);

        $profileData = [
            'user_id' => $user->id,
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'sex' => $validated['sex'],
            'contact_no' => $validated['contact_no'],
            'birthday' => $validated['birthday'],
            'age' => $this->calculateAge($birthday),
            'province' => $validated['province'],
            'municipality' => $validated['municipality'],
            'barangay' => $validated['barangay'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'registration_status' => 'pending',
        ];

        if ($role === User::ROLE_BUYER) {
            $profileData['upload_id'] = $this->storeRegistrationFile($request, 'upload_id');
            Buyer::create($profileData);
        }

        if ($role === User::ROLE_SELLER) {
            $profileData['business_name'] = $validated['business_name'];
            $profileData['line_of_business'] = $validated['line_of_business'];
            $profileData['upload_id'] = $this->storeRegistrationFile($request, 'upload_id');
            $profileData['upload_business_permit'] = $this->storeRegistrationFile($request, 'upload_business_permit');
            Seller::create($profileData);
        }

        if ($role === User::ROLE_RIDER) {
            $profileData['vehicle'] = $validated['vehicle'];
            $profileData['plate_number'] = $validated['plate_number'];
            $profileData['upload_or_cr'] = $this->storeRegistrationFile($request, 'upload_or_cr');
            $profileData['upload_id_license'] = $this->storeRegistrationFile($request, 'upload_id_license');
            Rider::create($profileData);
        }

        if ($role === User::ROLE_LOGISTICS) {
            $profileData['business_name'] = $validated['business_name'] ?? null;
            $profileData['upload_id'] = $this->storeRegistrationFile($request, 'upload_id');
            $profileData['upload_business_permit'] = $this->storeRegistrationFile($request, 'upload_business_permit');
            Logistics::create($profileData);
        }

        $message = 'Your registration has been submitted and is awaiting administrator approval.';

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'user' => $user,
            ], 201);
        }

        return redirect()->route('login')->with('status', $message);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();
        Auth::logout();

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['message' => 'Logout successful.']);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function calculateAge($birthday): int
    {
        if (! $birthday) {
            return 0;
        }

        $date = $birthday instanceof \DateTimeInterface
            ? $birthday
            : new \DateTimeImmutable($birthday);

        return $date->diff(new \DateTimeImmutable('now'))->y;
    }

    protected function storeRegistrationFile(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);

        return $file->store('registrations', 'public');
    }
}
