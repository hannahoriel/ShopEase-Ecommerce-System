<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Redirect According To User Role
        |--------------------------------------------------------------------------
        */

        return match (Auth::user()->role) {

            User::ROLE_ADMIN =>
                redirect()->route(
                    'admin.dashboard'
                ),

            User::ROLE_BUYER =>
                redirect()->route(
                    'buyer.dashboard'
                ),

            User::ROLE_SELLER =>
                redirect()->route(
                    'seller.dashboard'
                ),

            User::ROLE_LOGISTICS =>
                redirect()->route(
                    'logistics.dashboard'
                ),

            User::ROLE_RIDER =>
                redirect()->route(
                    'rider.dashboard'
                ),

            default =>
                redirect()->route(
                    'dashboard'
                ),

        };
    }


    /**
     * Register a new user and hold the account pending admin approval.
     */
    public function register(Request $request): RedirectResponse
    {
        $role = $request->input(
            'role',
            User::ROLE_BUYER
        );


        $rules = [

            'role' => [
                'required',
                'in:' . implode(',', [
                    User::ROLE_BUYER,
                    User::ROLE_SELLER,
                    User::ROLE_RIDER,
                ]),
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'middle_initial' => [
                'nullable',
                'string',
                'max:10',
            ],

            'sex' => [
                'required',
                'in:male,female,other',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],

            'contact_no' => [
                'required',
                'string',
                'max:20',
            ],

            'birthday' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'province' => [
                'required',
                'string',
                'max:255',
            ],

            'municipality' => [
                'required',
                'string',
                'max:255',
            ],

            'barangay' => [
                'required',
                'string',
                'max:255',
            ],

            'street' => [
                'required',
                'string',
                'max:255',
            ],

            'house_number' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],

            'upload_id' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ],

            'upload_id_license' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Seller-specific Fields
        |--------------------------------------------------------------------------
        */

        if ($role === User::ROLE_SELLER) {

            $rules['business_name'] = [
                'required',
                'string',
                'max:255',
            ];

            $rules['line_of_business'] = [
                'required',
                'string',
                'max:255',
            ];

            $rules['upload_business_permit'] = [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Rider-specific Fields
        |--------------------------------------------------------------------------
        */

        if ($role === User::ROLE_RIDER) {

            $rules['vehicle'] = [
                'required',
                'string',
                'max:255',
            ];

            $rules['plate_number'] = [
                'required',
                'string',
                'max:50',
            ];

            $rules['upload_or_cr'] = [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Registration
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            $rules
        );


        /*
        |--------------------------------------------------------------------------
        | Calculate Age
        |--------------------------------------------------------------------------
        */

        $birthday =
            $request->date(
                'birthday'
            );


        /*
        |--------------------------------------------------------------------------
        | Base User Profile
        |--------------------------------------------------------------------------
        */

        $profile = [

            'name' =>
                trim(
                    $validated['first_name']
                    . ' '
                    . $validated['last_name']
                ),

            'first_name' =>
                $validated['first_name'],

            'last_name' =>
                $validated['last_name'],

            'middle_initial' =>
                $validated['middle_initial']
                ?? null,

            'sex' =>
                $validated['sex'],

            'email' =>
                $validated['email'],

            'contact_no' =>
                $validated['contact_no'],

            'birthday' =>
                $validated['birthday'],

            'age' =>
                $this->calculateAge(
                    $birthday
                ),

            'province' =>
                $validated['province'],

            'municipality' =>
                $validated['municipality'],

            'barangay' =>
                $validated['barangay'],

            'street' =>
                $validated['street'],

            'house_number' =>
                $validated['house_number'],

            'role' =>
                $validated['role'],

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'registration_status' =>
                'pending',
        ];


        /*
        |--------------------------------------------------------------------------
        | Seller Profile
        |--------------------------------------------------------------------------
        */

        if ($role === User::ROLE_SELLER) {

            $profile['business_name'] =
                $validated['business_name'];

            $profile['line_of_business'] =
                $validated['line_of_business'];
        }


        /*
        |--------------------------------------------------------------------------
        | Rider Profile
        |--------------------------------------------------------------------------
        */

        if ($role === User::ROLE_RIDER) {

            $profile['vehicle'] =
                $validated['vehicle'];

            $profile['plate_number'] =
                $validated['plate_number'];
        }


        /*
        |--------------------------------------------------------------------------
        | Store Uploaded Files
        |--------------------------------------------------------------------------
        */

        $profile['upload_id'] =
            $this->storeRegistrationFile(
                $request,
                'upload_id'
            );


        $profile['upload_id_license'] =
            $this->storeRegistrationFile(
                $request,
                'upload_id_license'
            );


        if ($role === User::ROLE_SELLER) {

            $profile['upload_business_permit'] =
                $this->storeRegistrationFile(
                    $request,
                    'upload_business_permit'
                );
        }


        if ($role === User::ROLE_RIDER) {

            $profile['upload_or_cr'] =
                $this->storeRegistrationFile(
                    $request,
                    'upload_or_cr'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        User::create(
            $profile
        );


        /*
        |--------------------------------------------------------------------------
        | Return To Login
        |--------------------------------------------------------------------------
        |
        | This generic registration flow still returns to login.
        | Your specialized Buyer/Seller registration flows use
        | their own routes and registration pages.
        |
        */

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Your registration has been submitted and is awaiting administrator approval.'
            );
    }


    /**
     * Log the user out of the application.
     */
    public function logout(
        Request $request
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Invalidate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Regenerate CSRF Token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Return To LANDING PAGE
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            'landing.page'
        );
    }


    /**
     * Calculate age from birthday.
     */
    protected function calculateAge(
        $birthday
    ): int {

        if (! $birthday) {

            return 0;
        }


        $date =
            $birthday instanceof \DateTimeInterface
                ? $birthday
                : new \DateTimeImmutable(
                    $birthday
                );


        return $date
            ->diff(
                new \DateTimeImmutable('now')
            )
            ->y;
    }


    /**
     * Store registration file.
     */
    protected function storeRegistrationFile(
        Request $request,
        string $field
    ): ?string {

        if (! $request->hasFile($field)) {

            return null;
        }


        $file =
            $request->file(
                $field
            );


        return $file->store(
            'registrations',
            'public'
        );
    }
}