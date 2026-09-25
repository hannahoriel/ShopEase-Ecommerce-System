<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OrderStatusController;
use App\Http\Controllers\Seller\ShippingStatusController;
use App\Http\Controllers\Seller\InventoryController;
use App\Http\Controllers\Api\Seller\InventoryController as ApiSellerInventoryController;
use App\Models\User;
use App\Models\Admin\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| HOME / LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect()->route('login');

})->name('landing.page');


Route::get('/auth/login', function () {

    return view(
        'auth.login'
    );

})->middleware('guest')
  ->name('login');


/*
|--------------------------------------------------------------------------
| LOGIN SUBMIT
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [
    AuthController::class,
    'login'
])
    ->middleware('guest')
    ->name('login.attempt');


/*
|--------------------------------------------------------------------------
| REGISTER PAGE
|--------------------------------------------------------------------------
*/

Route::get('/auth/register', function () {

    return view(
        'auth.register'
    );

})->middleware('guest')
  ->name('register');


/*
|--------------------------------------------------------------------------
| REGISTER SUBMIT
|--------------------------------------------------------------------------
*/

Route::post('/auth/register', [
    AuthController::class,
    'register'
])
    ->middleware('guest')
    ->name('register.attempt');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/auth/logout', function (
    Request $request
) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');

})->middleware('auth')
  ->name('logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

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
            abort(
                403,
                'Your account does not have a valid role.'
            ),

    };

})->middleware('auth')
  ->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(DashboardController::class)->index();

})->middleware('auth')
  ->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| BUYER / SELLER / LOGISTICS / RIDER DASHBOARDS
|--------------------------------------------------------------------------
*/

foreach ([
    'buyer' => User::ROLE_BUYER,
    'logistics' => User::ROLE_LOGISTICS,
    'rider' => User::ROLE_RIDER,
] as $dashboard => $role) {

    Route::get(
        "/$dashboard/dashboard",
        function () use (
            $dashboard,
            $role
        ) {

            abort_unless(
                Auth::user()->role === $role,
                403
            );

            return view(
                "pages.$dashboard.dashboard"
            );

        }
    )
        ->middleware('auth')
        ->name("$dashboard.dashboard");

}

Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])
    ->middleware('auth')
    ->name('seller.dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN PAGES
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| ADMIN REGISTRATIONS
|--------------------------------------------------------------------------
*/

Route::get('/admin/registrations', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(
        RegistrationController::class
    )->index(request());

})->middleware('auth')
  ->name('admin.registrations');


Route::get('/admin/registrations/{registration}', [
    RegistrationController::class,
    'show',
])->middleware('auth')
    ->name('admin.registrations.show');


Route::get('/admin/registrations/approved/list', [
    RegistrationController::class,
    'approvedArchive',
])->middleware('auth')
    ->name('admin.registrations.approved.list');


Route::get('/admin/registrations/rejected/list', [
    RegistrationController::class,
    'rejectedArchive',
])->middleware('auth')
    ->name('admin.registrations.rejected.list');


Route::post('/admin/registrations/{registration}/approve', [
    RegistrationController::class,
    'approve',
])->middleware('auth')
    ->name('admin.registrations.approve');


Route::post('/admin/registrations/{registration}/reject', [
    RegistrationController::class,
    'reject',
])->middleware('auth')
    ->name('admin.registrations.reject');


/*
|--------------------------------------------------------------------------
| ADMIN USER MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::get('/admin/user-management', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(
        UserManagementController::class
    )->index();

})->middleware('auth')
  ->name('admin.user.management');


Route::get('/admin/user-management/list', [
    UserManagementController::class,
    'list',
])->middleware('auth')
    ->name('admin.user.management.list');


Route::get('/admin/user-management/{user}', [
    UserManagementController::class,
    'show',
])->middleware('auth')
    ->name('admin.user.management.show');


Route::patch('/admin/user-management/{user}/status', [
    UserManagementController::class,
    'updateStatus',
])->middleware('auth')
    ->name('admin.user.management.status');


Route::get('/seller/register', function () {

    return view(
        'auth.seller-signup.seller-register',
        [
            'sellerData' => session(
                'seller_registration',
                []
            ),
        ]
    );

})->middleware('guest')
  ->name('seller.register');


/*
|--------------------------------------------------------------------------
| SELLER STEP 1 - POST
|--------------------------------------------------------------------------
*/

Route::post('/seller/register', function (
    Request $request
) {

    $sellerData = session(
        'seller_registration',
        []
    );

    $previousEmail =
        $sellerData['email'] ?? null;

    $draftId =
        $sellerData['draft_id']
        ?? (string) Str::uuid();

    $sellerData['draft_id'] =
        $draftId;

    $stepOneData =
        $request->except([
            '_token',
            'valid_id',
        ]);

    $sellerData =
        array_merge(
            $sellerData,
            $stepOneData
        );

    $newEmail =
        $sellerData['email'] ?? null;


    /*
    |--------------------------------------------------------------------------
    | Invalidate old verification when email changes
    |--------------------------------------------------------------------------
    */

    if (
        $previousEmail !== null &&
        $newEmail !== null &&
        strtolower(trim($previousEmail)) !==
        strtolower(trim($newEmail))
    ) {

        session()->forget(
            'seller_registration_verification'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Valid ID Upload
    |--------------------------------------------------------------------------
    */

    if (
        $request->hasFile(
            'valid_id'
        )
    ) {

        if (
            !empty(
                $sellerData['valid_id_path']
            )
        ) {

            Storage::disk('public')->delete(
                $sellerData[
                    'valid_id_path'
                ]
            );

        }


        $file =
            $request->file(
                'valid_id'
            );


        $path =
            $file->store(
                "seller-registration/{$draftId}",
                'public'
            );


        $sellerData[
            'valid_id_path'
        ] =
            $path;


        $sellerData[
            'valid_id_original_name'
        ] =
            $file->getClientOriginalName();


        $sellerData[
            'valid_id_mime'
        ] =
            $file->getMimeType();

    }


    session([
        'seller_registration' =>
            $sellerData,
    ]);


    return redirect()->route(
        'seller.business.register'
    );

})->middleware('guest')
  ->name('seller.register.submit');


/*
|--------------------------------------------------------------------------
| SELLER STEP 2 - GET
|--------------------------------------------------------------------------
*/

Route::get('/seller/register/business', function () {

    return view(
        'auth.seller-signup.seller-business-register',
        [
            'sellerData' => session(
                'seller_registration',
                []
            ),
        ]
    );

})->middleware('guest')
  ->name('seller.business.register');


/*
|--------------------------------------------------------------------------
| SELLER STEP 2 - POST
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/register/business',
    function (Request $request) {

        $sellerData = session(
            'seller_registration',
            []
        );


        $draftId =
            $sellerData['draft_id']
            ?? (string) Str::uuid();


        $sellerData['draft_id'] =
            $draftId;


        $stepTwoData =
            $request->except([
                '_token',
                'business_permit',
            ]);


        $categories =
            $request->input(
                'categories',
                []
            );


        $stepTwoData['categories'] =
            is_array($categories)
                ? $categories
                : [];


        $sellerData =
            array_merge(
                $sellerData,
                $stepTwoData
            );


        if (
            $request->hasFile(
                'business_permit'
            )
        ) {

            if (
                !empty(
                    $sellerData[
                        'business_permit_path'
                    ]
                )
            ) {

                Storage::disk('public')->delete(
                    $sellerData[
                        'business_permit_path'
                    ]
                );

            }


            $file =
                $request->file(
                    'business_permit'
                );


            $path =
                $file->store(
                    "seller-registration/{$draftId}",
                    'public'
                );


            $sellerData[
                'business_permit_path'
            ] =
                $path;


            $sellerData[
                'business_permit_original_name'
            ] =
                $file->getClientOriginalName();


            $sellerData[
                'business_permit_mime'
            ] =
                $file->getMimeType();

        }


        session([
            'seller_registration' =>
                $sellerData,
        ]);


        return redirect()->route(
            'seller.review.register'
        );

    }
)
    ->middleware('guest')
    ->name('seller.business.submit');


/*
|--------------------------------------------------------------------------
| SELLER STEP 2 COMPATIBILITY ROUTE
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/register/review',
    function (Request $request) {

        $sellerData = session(
            'seller_registration',
            []
        );


        $draftId =
            $sellerData['draft_id']
            ?? (string) Str::uuid();


        $sellerData['draft_id'] =
            $draftId;


        $stepTwoData =
            $request->except([
                '_token',
                'business_permit',
            ]);


        $categories =
            $request->input(
                'categories',
                []
            );


        $stepTwoData['categories'] =
            is_array($categories)
                ? $categories
                : [];


        $sellerData =
            array_merge(
                $sellerData,
                $stepTwoData
            );


        if (
            $request->hasFile(
                'business_permit'
            )
        ) {

            if (
                !empty(
                    $sellerData[
                        'business_permit_path'
                    ]
                )
            ) {

                Storage::disk('public')->delete(
                    $sellerData[
                        'business_permit_path'
                    ]
                );

            }


            $file =
                $request->file(
                    'business_permit'
                );


            $path =
                $file->store(
                    "seller-registration/{$draftId}",
                    'public'
                );


            $sellerData[
                'business_permit_path'
            ] =
                $path;


            $sellerData[
                'business_permit_original_name'
            ] =
                $file->getClientOriginalName();


            $sellerData[
                'business_permit_mime'
            ] =
                $file->getMimeType();

        }


        session([
            'seller_registration' =>
                $sellerData,
        ]);


        return redirect()->route(
            'seller.review.register'
        );

    }
)
    ->middleware('guest')
    ->name('seller.review.submit');


/*
|--------------------------------------------------------------------------
| SELLER STEP 3 - REVIEW GET
|--------------------------------------------------------------------------
*/

Route::get('/seller/register/review', function () {

    $sellerData = session(
        'seller_registration',
        []
    );


    $sellerData['valid_id_path'] =
        $sellerData['valid_id_path']
        ?? $sellerData['valid_id']
        ?? $sellerData['upload_id']
        ?? null;


    $sellerData['business_permit_path'] =
        $sellerData['business_permit_path']
        ?? $sellerData['business_permit']
        ?? $sellerData['upload_business_permit']
        ?? null;


    $validIdUrl = null;


    if (
        !empty(
            $sellerData['valid_id_path']
        )
    ) {

        $validIdUrl =
            Storage::url(
                $sellerData[
                    'valid_id_path'
                ]
            );

    }


    $businessPermitUrl = null;


    if (
        !empty(
            $sellerData[
                'business_permit_path'
            ]
        )
    ) {

        $businessPermitUrl =
            Storage::url(
                $sellerData[
                    'business_permit_path'
                ]
            );

    }


    return view(
        'auth.seller-signup.seller-review-register',
        [
            'sellerData' =>
                $sellerData,

            'validIdUrl' =>
                $validIdUrl,

            'businessPermitUrl' =>
                $businessPermitUrl,
        ]
    );

})->middleware('guest')
  ->name('seller.review.register');


/*
|--------------------------------------------------------------------------
| SELLER EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

Route::post('/seller/register/send-code', function (Request $request) {

    $sellerData =
        session(
            'seller_registration',
            []
        );


    $email =
        $sellerData['email'] ?? null;


    if (!$email) {

        return response()->json([
            'message' =>
                'Your seller registration session has expired. Please start again.',
        ], 422);

    }


    $previousVerification =
        session(
            'seller_registration_verification'
        );


    if (
        $previousVerification
        && !empty(
            $previousVerification['resend_available_at']
        )
        && now()->lessThan(
            $previousVerification['resend_available_at']
        )
    ) {

        return response()->json([
            'retry_after' =>
                now()->diffInSeconds(
                    $previousVerification[
                        'resend_available_at'
                    ]
                ),
        ], 429);

    }


    $hasRejectedRegistration =
        Registration::where(
            'email',
            $email
        )
        ->where(
            'status',
            'rejected'
        )
        ->exists();


    if (
        User::where(
            'email',
            $email
        )
        ->where(
            'registration_status',
            '!=',
            'rejected'
        )
        ->exists()
        && ! $hasRejectedRegistration
    ) {

        return response()->json([
            'message' =>
                'This email address is already registered. Please use another email address or log in.',
        ], 422);

    }


    $code =
        (string) random_int(
            100000,
            999999
        );


    $isResend =
        !empty(
            $previousVerification
        );


    session([
        'seller_registration_verification' => [

            'email' =>
                $email,

            'code' =>
                Hash::make(
                    $code
                ),

            'expires_at' =>
                now()->addMinutes(
                    10
                ),

            'resend_available_at' =>
                now()->addMinutes(
                    $isResend
                        ? 10
                        : 2
                ),

            'verified' =>
                false,
        ],
    ]);


    Mail::to(
        $email
    )->send(
        new \App\Mail\SellerVerificationCode(
            $code
        )
    );


    return response()->json([
        'message' =>
            'A verification code has been sent to your email address.',
    ]);

})->middleware('guest')
  ->name('seller.register.send-code');


/*
|--------------------------------------------------------------------------
| SELLER VERIFY CODE
|--------------------------------------------------------------------------
*/

Route::post('/seller/register/verify-code', function (Request $request) {

    $validated =
        $request->validate([
            'code' => [
                'required',
                'digits:6'
            ],
        ]);


    $verification =
        session(
            'seller_registration_verification'
        );


    if (
        !$verification
        || now()->greaterThan(
            $verification['expires_at']
        )
        || !Hash::check(
            $validated['code'],
            $verification['code']
        )
    ) {

        return response()->json([
            'message' =>
                'The verification code is invalid or expired.',
        ], 422);

    }


    session()->put(
        'seller_registration_verification.verified',
        true
    );


    return response()->json([
        'message' =>
            'Email verified successfully.',
    ]);

})->middleware('guest')
  ->name('seller.register.verify-code');


/*
|--------------------------------------------------------------------------
| SELLER FINAL SUBMIT
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/register/complete',
    function (Request $request) {

        $sellerData =
            session(
                'seller_registration',
                []
            );


        if (
            empty($sellerData)
        ) {

            return redirect()
                ->route(
                    'seller.register'
                )
                ->with(
                    'error',
                    'Your seller registration session has expired. Please start again.'
                );

        }


        $sellerEmail =
            $sellerData['email'] ?? '';


        $hasRejectedRegistration =
            Registration::where(
                'email',
                $sellerEmail
            )
            ->where(
                'status',
                'rejected'
            )
            ->exists();


        if (
            User::where(
                'email',
                $sellerEmail
            )
            ->where(
                'registration_status',
                '!=',
                'rejected'
            )
            ->exists()
            && ! $hasRejectedRegistration
        ) {

            return redirect()
                ->route(
                    'seller.register'
                )
                ->with(
                    'error',
                    'This email address is already registered. Please use another email address or log in.'
                );

        }


        $verification =
            session(
                'seller_registration_verification'
            );


        if (
            !$verification
            || ($verification['email'] ?? null)
                !== ($sellerData['email'] ?? null)
            || empty(
                $verification['verified']
            )
        ) {

            return redirect()
                ->route(
                    'seller.review.register'
                )
                ->with(
                    'error',
                    'Please verify your email address before submitting your registration.'
                );

        }


        $finalData =
            $request->except([
                '_token',
                'valid_id',
                'business_permit',
            ]);


        $sellerData =
            array_merge(
                $sellerData,
                $finalData
            );


        app(
            AuthController::class
        )->completeSellerRegistration(
            $sellerData
        );


        session()->forget(
            'seller_registration'
        );


        session()->forget(
            'seller_registration_verification'
        );


        return redirect()
            ->route('login')
            ->with(
                'success',
                'Seller registration submitted successfully.'
            );

    }
)
    ->middleware('guest')
    ->name('seller.register.complete');


/*
|--------------------------------------------------------------------------
| SELLER EXIT / RESET
|--------------------------------------------------------------------------
*/

Route::get('/seller/register/exit', function () {

    $sellerData =
        session(
            'seller_registration',
            []
        );


    if (
        !empty(
            $sellerData['valid_id_path']
        )
    ) {

        Storage::disk('public')->delete(
            $sellerData[
                'valid_id_path'
            ]
        );

    }


    if (
        !empty(
            $sellerData['business_permit_path']
        )
    ) {

        Storage::disk('public')->delete(
            $sellerData[
                'business_permit_path'
            ]
        );

    }


    if (
        !empty(
            $sellerData['draft_id']
        )
    ) {

        Storage::disk('public')->deleteDirectory(
            "seller-registration/{$sellerData['draft_id']}"
        );

    }


    session()->forget(
        'seller_registration'
    );


    session()->forget(
        'seller_registration_verification'
    );


    return redirect()->route(
        'landing.page'
    );

})->middleware('guest')
  ->name('seller.register.exit');


/*
|--------------------------------------------------------------------------
| BUYER REGISTRATION
|--------------------------------------------------------------------------
|
| STEP 1 = Buyer Information
| STEP 2 = Review Information
|
*/


/*
|--------------------------------------------------------------------------
| BUYER STEP 1 - GET
|--------------------------------------------------------------------------
*/

Route::get('/buyer/register', function () {

    return view(
        'auth.buyer-signup.buyer-register',
        [
            'buyerData' => session(
                'buyer_registration',
                []
            ),
        ]
    );

})->middleware('guest')
  ->name('buyer.register');


/*
|--------------------------------------------------------------------------
| BUYER STEP 1 - POST
|--------------------------------------------------------------------------
*/

Route::post('/buyer/register', function (
    Request $request
) {

    $buyerData =
        session(
            'buyer_registration',
            []
        );


    /*
    |--------------------------------------------------------------------------
    | Remember the previous email BEFORE merging
    |--------------------------------------------------------------------------
    */

    $previousEmail =
        $buyerData['email'] ?? null;


    /*
    |--------------------------------------------------------------------------
    | Draft ID
    |--------------------------------------------------------------------------
    */

    $draftId =
        $buyerData['draft_id']
        ?? (string) Str::uuid();


    $buyerData['draft_id'] =
        $draftId;


    /*
    |--------------------------------------------------------------------------
    | Buyer Information
    |--------------------------------------------------------------------------
    */

    $stepOneData =
        $request->except([
            '_token',
            'valid_id',
        ]);


    /*
    |--------------------------------------------------------------------------
    | Merge Buyer Data
    |--------------------------------------------------------------------------
    */

    $buyerData =
        array_merge(
            $buyerData,
            $stepOneData
        );


    /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Invalidate old verification when buyer changes email
    |--------------------------------------------------------------------------
    */

    $newEmail =
        $buyerData['email'] ?? null;


    if (
        $previousEmail !== null &&
        $newEmail !== null &&
        strtolower(trim($previousEmail)) !==
        strtolower(trim($newEmail))
    ) {

        session()->forget(
            'buyer_registration_verification'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Valid ID
    |--------------------------------------------------------------------------
    */

    if (
        $request->hasFile(
            'valid_id'
        )
    ) {

        if (
            !empty(
                $buyerData[
                    'valid_id_path'
                ]
            )
        ) {

            Storage::disk('public')->delete(
                $buyerData[
                    'valid_id_path'
                ]
            );

        }


        $file =
            $request->file(
                'valid_id'
            );


        $path =
            $file->store(
                "buyer-registration/{$draftId}",
                'public'
            );


        $buyerData[
            'valid_id_path'
        ] =
            $path;


        $buyerData[
            'valid_id_original_name'
        ] =
            $file->getClientOriginalName();


        $buyerData[
            'valid_id_mime'
        ] =
            $file->getMimeType();

    }


    /*
    |--------------------------------------------------------------------------
    | Save Buyer Session
    |--------------------------------------------------------------------------
    */

    session([
        'buyer_registration' =>
            $buyerData,
    ]);


    /*
    |--------------------------------------------------------------------------
    | Continue To Review
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'buyer.review.register'
    );

})->middleware('guest')
  ->name('buyer.register.submit');


/*
|--------------------------------------------------------------------------
| BUYER STEP 2 - REVIEW GET
|--------------------------------------------------------------------------
*/

Route::get('/buyer/register/review', function () {

    $buyerData =
        session(
            'buyer_registration',
            []
        );


    $validIdUrl =
        null;


    if (
        !empty(
            $buyerData[
                'valid_id_path'
            ]
        )
    ) {

        $validIdUrl =
            Storage::url(
                $buyerData[
                    'valid_id_path'
                ]
            );

    }


    return view(
        'auth.buyer-signup.buyer-review-register',
        [
            'buyerData' =>
                $buyerData,

            'validIdUrl' =>
                $validIdUrl,
        ]
    );

})->middleware('guest')
  ->name('buyer.review.register');


/*
|--------------------------------------------------------------------------
| BUYER EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

Route::post('/buyer/register/send-code', function () {

    /*
    |--------------------------------------------------------------------------
    | Current Buyer Registration
    |--------------------------------------------------------------------------
    */

    $buyerData =
        session(
            'buyer_registration',
            []
        );


    /*
    |--------------------------------------------------------------------------
    | Current Email
    |--------------------------------------------------------------------------
    */

    $email =
        $buyerData['email'] ?? null;


    if (!$email) {

        return response()->json([
            'message' =>
                'Your buyer registration session has expired. Please start again.',
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | Previous Verification
    |--------------------------------------------------------------------------
    */

    $previousVerification =
        session(
            'buyer_registration_verification'
        );


    /*
    |--------------------------------------------------------------------------
    | Resend Cooldown
    |--------------------------------------------------------------------------
    */

    if (
        $previousVerification
        && !empty(
            $previousVerification['resend_available_at']
        )
        && now()->lessThan(
            $previousVerification[
                'resend_available_at'
            ]
        )
    ) {

        return response()->json([
            'retry_after' =>
                now()->diffInSeconds(
                    $previousVerification[
                        'resend_available_at'
                    ]
                ),
        ], 429);

    }


    /*
    |--------------------------------------------------------------------------
    | Allow previously rejected registrations to reuse the email
    |--------------------------------------------------------------------------
    */

    $hasRejectedRegistration =
        Registration::where(
            'email',
            $email
        )
        ->where(
            'status',
            'rejected'
        )
        ->exists();


    /*
    |--------------------------------------------------------------------------
    | Existing User Check
    |--------------------------------------------------------------------------
    */

    if (
        User::where(
            'email',
            $email
        )
        ->where(
            'registration_status',
            '!=',
            'rejected'
        )
        ->exists()
        && ! $hasRejectedRegistration
    ) {

        return response()->json([
            'message' =>
                'This email address is already registered. Please use another email address or log in.',
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | Generate Verification Code
    |--------------------------------------------------------------------------
    */

    $code =
        (string) random_int(
            100000,
            999999
        );


    /*
    |--------------------------------------------------------------------------
    | Determine Resend
    |--------------------------------------------------------------------------
    */

    $isResend =
        !empty(
            $previousVerification
        );


    /*
    |--------------------------------------------------------------------------
    | Save Verification Session
    |--------------------------------------------------------------------------
    */

    session([
        'buyer_registration_verification' => [

            'email' =>
                $email,

            'code' =>
                Hash::make(
                    $code
                ),

            'expires_at' =>
                now()->addMinutes(
                    10
                ),

            'resend_available_at' =>
                now()->addMinutes(
                    $isResend
                        ? 10
                        : 2
                ),

            'verified' =>
                false,
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | SEND BUYER VERIFICATION EMAIL
    |--------------------------------------------------------------------------
    */

    Mail::to(
        $email
    )->send(
        new \App\Mail\BuyerVerificationCode(
            $code
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'message' =>
            'A verification code has been sent to your email address.',
    ]);

})->middleware('guest')
  ->name('buyer.register.send-code');


/*
|--------------------------------------------------------------------------
| BUYER VERIFY CODE
|--------------------------------------------------------------------------
*/

Route::post('/buyer/register/verify-code', function (
    Request $request
) {

    $validated =
        $request->validate([
            'code' => [
                'required',
                'digits:6'
            ],
        ]);


    $verification =
        session(
            'buyer_registration_verification'
        );


    if (
        !$verification
        || now()->greaterThan(
            $verification[
                'expires_at'
            ]
        )
        || !Hash::check(
            $validated['code'],
            $verification['code']
        )
    ) {

        return response()->json([
            'message' =>
                'The verification code is invalid or expired.',
        ], 422);

    }


    session()->put(
        'buyer_registration_verification.verified',
        true
    );


    return response()->json([
        'message' =>
            'Email verified successfully.',
    ]);

})->middleware('guest')
  ->name('buyer.register.verify-code');


/*
|--------------------------------------------------------------------------
| BUYER FINAL SUBMIT
|--------------------------------------------------------------------------
*/

Route::post(
    '/buyer/register/complete',
    function (Request $request) {

        /*
        |--------------------------------------------------------------------------
        | Existing Buyer Registration
        |--------------------------------------------------------------------------
        */

        $buyerData =
            session(
                'buyer_registration',
                []
            );


        /*
        |--------------------------------------------------------------------------
        | Check Registration Session
        |--------------------------------------------------------------------------
        */

        if (
            empty($buyerData)
        ) {

            return redirect()
                ->route(
                    'buyer.register'
                )
                ->with(
                    'error',
                    'Your buyer registration session has expired. Please start again.'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Verification must belong to CURRENT email
        |--------------------------------------------------------------------------
        */

        $verification =
            session(
                'buyer_registration_verification'
            );


        if (
            !$verification
            || ($verification['email'] ?? null)
                !== ($buyerData['email'] ?? null)
            || empty(
                $verification['verified']
            )
        ) {

            return redirect()
                ->route(
                    'buyer.review.register'
                )
                ->with(
                    'error',
                    'Please verify your email address before submitting your registration.'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Final Data
        |--------------------------------------------------------------------------
        */

        $finalData =
            $request->except([
                '_token',
                'valid_id',
            ]);


        $buyerData =
            array_merge(
                $buyerData,
                $finalData
            );


        app(
            AuthController::class
        )->completeBuyerRegistration(
            $buyerData
        );


        /*
        |--------------------------------------------------------------------------
        | Clear Registration Session
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'buyer_registration'
        );


        session()->forget(
            'buyer_registration_verification'
        );


        /*
        |--------------------------------------------------------------------------
        | Return To Landing Page
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Buyer registration submitted successfully.'
            );

    }
)
    ->middleware('guest')
    ->name('buyer.register.complete');


/*
|--------------------------------------------------------------------------
| BUYER EXIT / RESET
|--------------------------------------------------------------------------
*/

Route::get('/buyer/register/exit', function () {

    $buyerData =
        session(
            'buyer_registration',
            []
        );


    /*
    |--------------------------------------------------------------------------
    | Delete Valid ID
    |--------------------------------------------------------------------------
    */

    if (
        !empty(
            $buyerData[
                'valid_id_path'
            ]
        )
    ) {

        Storage::disk('public')->delete(
            $buyerData[
                'valid_id_path'
            ]
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Delete Buyer Registration Folder
    |--------------------------------------------------------------------------
    */

    if (
        !empty(
            $buyerData[
                'draft_id'
            ]
        )
    ) {

        Storage::disk('public')->deleteDirectory(
            "buyer-registration/{$buyerData['draft_id']}"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Clear Buyer Session
    |--------------------------------------------------------------------------
    */

    session()->forget(
        'buyer_registration'
    );


    session()->forget(
        'buyer_registration_verification'
    );


    /*
    |--------------------------------------------------------------------------
    | Return To Landing Page
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'landing.page'
    );

})->middleware('guest')
  ->name('buyer.register.exit');


/*
|--------------------------------------------------------------------------
| SELLER INVENTORY
|--------------------------------------------------------------------------
*/

Route::get(
    '/seller/inventory',
    [InventoryController::class, 'index']
)
    ->middleware('auth')
    ->name('seller.inventory');

Route::middleware('auth')->group(function () {
    Route::post('/seller/inventory/products', [ApiSellerInventoryController::class, 'store'])
        ->name('seller.web.inventory.products.store');

    Route::patch('/seller/inventory/products/{product}/archive', [ApiSellerInventoryController::class, 'archive'])
        ->name('seller.web.inventory.products.archive');

    Route::patch('/seller/inventory/products/{product}/unarchive', [ApiSellerInventoryController::class, 'unarchive'])
        ->name('seller.web.inventory.products.unarchive');

    Route::delete('/seller/inventory/products/{product}', [ApiSellerInventoryController::class, 'destroy'])
        ->name('seller.web.inventory.products.destroy');
});


/*
|--------------------------------------------------------------------------
| SELLER ORDER STATUS
|--------------------------------------------------------------------------
*/

Route::get('/seller/order-status', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_SELLER,
        403
    );

    return app(OrderStatusController::class)->page(request());

})->middleware('auth')
  ->name('seller.order.status');


/*
|--------------------------------------------------------------------------
| SELLER SHIPPING STATUS
|--------------------------------------------------------------------------
*/

Route::get('/seller/shipping-status', function () {
    abort_unless(Auth::user()->role === User::ROLE_SELLER, 403);

    return app(ShippingStatusController::class)->page(request());
})->middleware('auth')
  ->name('seller.shipping.status');

Route::get('/admin/seller-compliance', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(\App\Http\Controllers\Api\Admin\SellerComplianceController::class)->page(request());
})->middleware('auth')
  ->name('admin.seller.compliance');

Route::middleware('auth')->group(function () {
    Route::get('/admin/seller-compliance/data', [\App\Http\Controllers\Api\Admin\SellerComplianceController::class, 'index'])
        ->name('admin.seller.compliance.data');

    Route::get('/admin/seller-compliance/data/{seller}', [\App\Http\Controllers\Api\Admin\SellerComplianceController::class, 'show'])
        ->name('admin.seller.compliance.show');

    Route::post('/admin/seller-compliance/data/products/{product}/approve', [\App\Http\Controllers\Api\Admin\SellerComplianceController::class, 'approveProduct'])
        ->name('admin.seller.compliance.approve');

    Route::post('/admin/seller-compliance/data/products/{product}/warn', [\App\Http\Controllers\Api\Admin\SellerComplianceController::class, 'warnProduct'])
        ->name('admin.seller.compliance.warn');

    Route::post('/admin/seller-compliance/data/products/{product}/remove', [\App\Http\Controllers\Api\Admin\SellerComplianceController::class, 'removeProduct'])
        ->name('admin.seller.compliance.remove');
});

Route::get('/admin/complaints-disputes', function () {
    return view('pages.admin.complaints-disputes');
})->name('admin.complaints.disputes');

Route::get('/admin/commission', function () {
    return view('pages.admin.commission');
})->name('admin.commission');

Route::get('/admin/logistics-management', function () {
    return view('pages.admin.logistics-management');
})->name('admin.logistics.management');

Route::get('/admin/platform-settings', function () {
    return view('pages.admin.platform-settings');
})->name('admin.platform.settings');
