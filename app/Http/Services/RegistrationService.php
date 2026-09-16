<?php

namespace App\Http\Services;

use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController; // not used, remove if present
use App\Models\Admin\Registration;
use App\Models\Buyer\Buyer;
use App\Models\Logistics\Logistics;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    /**
     * Validate + register a new user (buyer/seller/logistics/rider),
     * from the multi-purpose registration form. Returns the created User.
     */
    public function register(Request $request): User
    {
        $role = $request->input('role', User::ROLE_BUYER);

        $rejectedRegistration = Registration::where('email', $request->input('email'))
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        $rejectedUser = User::where('email', $request->input('email'))
            ->where(function ($query) use ($rejectedRegistration) {
                $query->where('registration_status', 'rejected');

                if ($rejectedRegistration?->user_id) {
                    $query->orWhereKey($rejectedRegistration->user_id);
                }
            })
            ->first();

        if ($rejectedRegistration && ! $rejectedUser) {
            $rejectedUser = User::where('email', $request->input('email'))->first();
        }

        $rules = [
            'role' => [
                'required',
                'in:' . implode(',', [
                    User::ROLE_BUYER,
                    User::ROLE_SELLER,
                    User::ROLE_LOGISTICS,
                    User::ROLE_RIDER,
                ]),
            ],
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'sex' => ['required', 'in:male,female,other'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($rejectedUser?->id),
            ],
            'contact_no' => ['required', 'string', 'max:20'],
            'birthday' => $this->birthdayValidationRules(),
            'province' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', ...$this->passwordRules()],
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

        $profile = [
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
        ];

        if ($role === User::ROLE_SELLER) {
            $profile['business_name'] = $validated['business_name'];
            $profile['store_name'] = $validated['business_name'];
            $profile['line_of_business'] = $validated['line_of_business'];
        }

        if ($role === User::ROLE_RIDER) {
            $profile['vehicle'] = $validated['vehicle'];
            $profile['plate_number'] = $validated['plate_number'];
        }

        $profile['upload_id'] = $this->storeRegistrationFile($request, 'upload_id');
        $profile['upload_id_license'] = $this->storeRegistrationFile($request, 'upload_id_license');

        if ($role === User::ROLE_SELLER) {
            $profile['upload_business_permit'] = $this->storeRegistrationFile($request, 'upload_business_permit');
        }

        if ($role === User::ROLE_RIDER) {
            $profile['upload_or_cr'] = $this->storeRegistrationFile($request, 'upload_or_cr');
        }

        return DB::transaction(function () use ($profile, $role) {
            $rejectedUser = User::where('email', $profile['email'])
                ->where(function ($query) use ($profile) {
                    $query->where('registration_status', 'rejected')
                        ->orWhereIn('id', Registration::where('email', $profile['email'])
                            ->where('status', 'rejected')
                            ->pluck('user_id'));
                })
                ->first();

            $user = $rejectedUser ?: User::create($this->userAttributes($profile));

            if ($rejectedUser) {
                $user->update($this->userAttributes($profile));
                Buyer::where('user_id', $user->id)->delete();
                Seller::where('user_id', $user->id)->delete();
                Rider::where('user_id', $user->id)->delete();
                Logistics::where('user_id', $user->id)->delete();
            }

            $this->createRegistration($user, $profile);

            $profile['user_id'] = $user->id;

            if ($role === User::ROLE_BUYER) {
                Buyer::create($profile);
            } elseif ($role === User::ROLE_SELLER) {
                Seller::create($profile);
            } elseif ($role === User::ROLE_RIDER) {
                Rider::create($profile);
            } elseif ($role === User::ROLE_LOGISTICS) {
                Logistics::create($profile);
            }

            return $user;
        });
    }

    public function completeBuyerRegistration(array $data): void
    {
        $data = $this->normalizeRegistrationData($data);
        validator($data, $this->registrationValidationRules())->validate();

        DB::transaction(function () use ($data): void {
            $user = $this->createRegisteredUser($data, User::ROLE_BUYER);
            $this->createRegistration($user, $data);

            Buyer::create([
                'user_id' => $user->id,
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'] ?? null,
                'sex' => $data['sex'],
                'contact_no' => $data['contact_no'],
                'birthday' => $data['birthday'],
                'age' => $this->calculateAge($data['birthday']),
                'province' => $data['province'],
                'municipality' => $data['municipality'],
                'barangay' => $data['barangay'],
                'street' => $data['street'] ?? null,
                'house_number' => $data['house_number'] ?? null,
                'upload_id' => $data['valid_id_path'] ?? null,
                'registration_status' => 'pending',
            ]);
        });
    }

    public function completeSellerRegistration(array $data): void
    {
        $data = $this->normalizeRegistrationData($data);
        validator($data, $this->registrationValidationRules())->validate();

        DB::transaction(function () use ($data): void {
            $user = $this->createRegisteredUser($data, User::ROLE_SELLER);
            $this->createRegistration($user, $data);

            Seller::create([
                'user_id' => $user->id,
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
                'middle_initial' => $data['middle_initial'] ?? null,
                'sex' => $data['sex'],
                'contact_no' => $data['contact_no'],
                'birthday' => $data['birthday'],
                'age' => $this->calculateAge($data['birthday']),
                'province' => $data['province'],
                'municipality' => $data['municipality'],
                'barangay' => $data['barangay'],
                'street' => $data['street'] ?? null,
                'house_number' => $data['house_number'] ?? null,
                'store_name' => $data['store_name'] ?? ($data['business_name'] ?? null),
                'business_name' => $data['business_name'] ?? null,
                'line_of_business' => $this->categoryValue($data),
                'upload_id' => $data['valid_id_path'] ?? null,
                'upload_business_permit' => $data['business_permit_path'] ?? null,
                'registration_status' => 'pending',
            ]);
        });
    }

    private function createRegisteredUser(array $data, string $role): User
    {
        $existingUser = User::where('email', $data['email'])->first();
        $rejectedRegistration = Registration::where('email', $data['email'])
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        $canReRegister = $existingUser
            && ($existingUser->registration_status === 'rejected'
                || $existingUser->id === $rejectedRegistration?->user_id
                || $rejectedRegistration !== null);

        if ($existingUser && ! $canReRegister) {
            throw ValidationException::withMessages([
                'email' => 'This email address is already registered. Please use another email address or log in.',
            ]);
        }

        $attributes = [
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? Str::random(40)),
            'role' => $role,
            'registration_status' => 'pending',
        ];

        if ($existingUser) {
            $existingUser->update($attributes);
            Buyer::where('user_id', $existingUser->id)->delete();
            Seller::where('user_id', $existingUser->id)->delete();
            Rider::where('user_id', $existingUser->id)->delete();
            Logistics::where('user_id', $existingUser->id)->delete();
            $user = $existingUser->fresh();
        } else {
            $user = User::create($attributes);
        }

        $this->createRegistration($user, $data);

        return $user;
    }

    private function createRegistration(User $user, array $data): void
    {
        $registration = Registration::where('user_id', $user->id)->latest('id')->first();

        if ($registration && $registration->status !== 'rejected') {
            return;
        }

        $attributes = [
            'user_id' => $user->id,
            'user_type' => $user->role,
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? $data['middle_initial'] ?? null,
            'sex' => $data['sex'],
            'birthdate' => $data['birthday'],
            'email' => $data['email'],
            'phone' => $data['contact_no'] ?? $data['phone'] ?? '',
            'password' => $user->getRawOriginal('password'),
            'province' => $data['province'],
            'municipality' => $data['municipality'],
            'barangay' => $data['barangay'],
            'street' => $data['street'] ?? '',
            'house_no' => $data['house_number'] ?? $data['house_no'] ?? null,
            'zip_code' => $data['zip_code'] ?? '0000',
            'business_name' => $data['business_name'] ?? $data['store_name'] ?? null,
            'business_category' => $this->categoryValue($data),
            'business_permit_path' => $data['business_permit_path'] ?? null,
            'valid_id_path' => $data['valid_id_path'] ?? $data['valid_id'] ?? $data['upload_id'] ?? '',
            'status' => 'pending',
        ];

        if ($registration) {
            $registration->update($attributes);
        } else {
            Registration::create($attributes);
        }
    }

    private function categoryValue(array $data): ?string
    {
        $categories = $data['categories'] ?? null;

        if (is_string($categories)) {
            return $categories !== '' ? $categories : null;
        }

        if (is_array($categories) && count($categories) > 0) {
            return implode(', ', array_filter($categories));
        }

        return $data['line_of_business'] ?? null;
    }

    private function userAttributes(array $attributes): array
    {
        return array_intersect_key($attributes, array_flip([
            'name', 'email', 'password', 'role', 'email_verified_at',
            'registration_status', 'approved_at', 'rejected_at', 'suspended_until',
        ]));
    }

    private function normalizeRegistrationData(array $data): array
    {
        $data = array_merge([
            'middle_initial' => $data['middle_name'] ?? null,
            'house_number' => $data['house_no'] ?? null,
            'valid_id_path' => $data['valid_id'] ?? null,
            'business_permit_path' => $data['business_permit'] ?? null,
        ], $data);

        $data['valid_id_path'] = $data['valid_id_path']
            ?: ($data['valid_id'] ?? $data['upload_id'] ?? null);

        $data['business_permit_path'] = $data['business_permit_path']
            ?: ($data['business_permit'] ?? $data['upload_business_permit'] ?? null);

        return $data;
    }

    private function passwordValidationRules(): array
    {
        return ['password' => ['required', 'confirmed', ...$this->passwordRules()]];
    }

    private function registrationValidationRules(): array
    {
        return array_merge(
            ['birthday' => $this->birthdayValidationRules()],
            $this->passwordValidationRules(),
        );
    }

    private function birthdayValidationRules(): array
    {
        return [
            'required', 'date', 'before_or_equal:today',
            'before_or_equal:' . now()->subYears(18)->toDateString(),
        ];
    }

    private function passwordRules(): array
    {
        return [Rules\Password::min(8)->mixedCase()->numbers()->symbols()];
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

        return $request->file($field)->store('registrations', 'public');
    }
}
