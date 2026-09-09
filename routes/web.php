<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RegistrationController;
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
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect()->route('login');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/auth/login', function () {

    return view('auth.login');

})->middleware('guest')
  ->name('login');


Route::post('/auth/login', [
    AuthController::class,
    'login'
])
    ->middleware('guest')
    ->name('login.attempt');


Route::get('/auth/register', function () {

    return view('auth.register');

})->middleware('guest')
  ->name('register');


Route::post('/auth/register', [
    AuthController::class,
    'register'
])
    ->middleware('guest')
    ->name('register.attempt');


Route::post('/auth/logout', [
    AuthController::class,
    'logout'
])
    ->middleware('auth')
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
    'seller' => User::ROLE_SELLER,
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


/*
|--------------------------------------------------------------------------
| ADMIN PAGES
|--------------------------------------------------------------------------
*/

Route::get('/admin/registrations', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(RegistrationController::class)->index(request());

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


Route::get('/admin/user-management', function () {

    abort_unless(
        Auth::user()->role === User::ROLE_ADMIN,
        403
    );

    return app(UserManagementController::class)->index();

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


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| SELLER REGISTRATION
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| STEP 1 = Seller Information
| STEP 2 = Business Information
| STEP 3 = Review Documents
|
*/


/*
|--------------------------------------------------------------------------
| SELLER STEP 1
| GET
|--------------------------------------------------------------------------
*/

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
| SELLER STEP 1
| POST
|--------------------------------------------------------------------------
*/

Route::post('/seller/register', function (
    Request $request
) {

    $sellerData = session(
        'seller_registration',
        []
    );


    /*
    |--------------------------------------------------------------------------
    | Draft ID
    |--------------------------------------------------------------------------
    */

    $draftId =
        $sellerData['draft_id']
        ?? (string) Str::uuid();

    $sellerData['draft_id'] =
        $draftId;


    /*
    |--------------------------------------------------------------------------
    | Step 1 Data
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Valid ID
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('valid_id')) {

        if (
            !empty(
                $sellerData['valid_id_path']
            )
        ) {

            Storage::disk('public')->delete(
                $sellerData['valid_id_path']
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


        $sellerData['valid_id_path'] =
            $path;

        $sellerData['valid_id_original_name'] =
            $file->getClientOriginalName();

        $sellerData['valid_id_mime'] =
            $file->getMimeType();

    }


    /*
    |--------------------------------------------------------------------------
    | Save Session
    |--------------------------------------------------------------------------
    */

    session([
        'seller_registration' =>
            $sellerData,
    ]);


    /*
    |--------------------------------------------------------------------------
    | Go To Step 2
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'seller.business.register'
    );

})->middleware('guest')
  ->name('seller.register.submit');


/*
|--------------------------------------------------------------------------
| SELLER STEP 2
| GET
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
| SELLER STEP 2
| POST
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/register/business',
    function (Request $request) {

        $sellerData = session(
            'seller_registration',
            []
        );


        /*
        |--------------------------------------------------------------------------
        | Draft ID
        |--------------------------------------------------------------------------
        */

        $draftId =
            $sellerData['draft_id']
            ?? (string) Str::uuid();

        $sellerData['draft_id'] =
            $draftId;


        /*
        |--------------------------------------------------------------------------
        | Business Information
        |--------------------------------------------------------------------------
        */

        $stepTwoData =
            $request->except([
                '_token',
                'business_permit',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories =
            $request->input(
                'categories',
                []
            );


        $stepTwoData['categories'] =
            is_array($categories)
                ? $categories
                : [];


        /*
        |--------------------------------------------------------------------------
        | Merge Data
        |--------------------------------------------------------------------------
        */

        $sellerData =
            array_merge(
                $sellerData,
                $stepTwoData
            );


        /*
        |--------------------------------------------------------------------------
        | Business Permit
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Save Session
        |--------------------------------------------------------------------------
        */

        session([
            'seller_registration' =>
                $sellerData,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Go To Step 3
        |--------------------------------------------------------------------------
        */

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
|
| Some Blade versions may submit to:
| seller.review.submit
|
*/

Route::post(
    '/seller/register/review',
    function (Request $request) {

        $sellerData = session(
            'seller_registration',
            []
        );


        /*
        |--------------------------------------------------------------------------
        | Draft ID
        |--------------------------------------------------------------------------
        */

        $draftId =
            $sellerData['draft_id']
            ?? (string) Str::uuid();

        $sellerData['draft_id'] =
            $draftId;


        /*
        |--------------------------------------------------------------------------
        | Step 2 Data
        |--------------------------------------------------------------------------
        */

        $stepTwoData =
            $request->except([
                '_token',
                'business_permit',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories =
            $request->input(
                'categories',
                []
            );


        $stepTwoData['categories'] =
            is_array($categories)
                ? $categories
                : [];


        /*
        |--------------------------------------------------------------------------
        | Merge
        |--------------------------------------------------------------------------
        */

        $sellerData =
            array_merge(
                $sellerData,
                $stepTwoData
            );


        /*
        |--------------------------------------------------------------------------
        | Business Permit
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Save Session
        |--------------------------------------------------------------------------
        */

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
| SELLER STEP 3
| GET
|--------------------------------------------------------------------------
*/

Route::get('/seller/register/review', function () {

    $sellerData = session(
        'seller_registration',
        []
    );


    /*
    |--------------------------------------------------------------------------
    | Valid ID URL
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Business Permit URL
    |--------------------------------------------------------------------------
    */

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

    $sellerData = session('seller_registration', []);
    $email = $sellerData['email'] ?? null;

    if (!$email) {
        return response()->json([
            'message' => 'Your seller registration session has expired. Please start again.',
        ], 422);
    }

    $previousVerification = session('seller_registration_verification');

    if (
        $previousVerification
        && !empty($previousVerification['resend_available_at'])
        && now()->lessThan($previousVerification['resend_available_at'])
    ) {
        return response()->json([
            'retry_after' => now()->diffInSeconds($previousVerification['resend_available_at']),
        ], 429);
    }

    $hasRejectedRegistration = Registration::where('email', $email)
        ->where('status', 'rejected')
        ->exists();

    if (User::where('email', $email)
        ->where('registration_status', '!=', 'rejected')
        ->exists() && ! $hasRejectedRegistration) {
        return response()->json([
            'message' => 'This email address is already registered. Please use another email address or log in.',
        ], 422);
    }

    $code = (string) random_int(100000, 999999);
    $isResend = !empty($previousVerification);

    session([
        'seller_registration_verification' => [
            'email' => $email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'resend_available_at' => now()->addMinutes($isResend ? 10 : 2),
            'verified' => false,
        ],
    ]);

    Mail::to($email)->send(new \App\Mail\SellerVerificationCode($code));

    return response()->json([
        'message' => 'A verification code has been sent to your email address.',
    ]);
})->middleware('guest')->name('seller.register.send-code');


Route::post('/seller/register/verify-code', function (Request $request) {

    $validated = $request->validate([
        'code' => ['required', 'digits:6'],
    ]);

    $verification = session('seller_registration_verification');

    if (
        !$verification
        || now()->greaterThan($verification['expires_at'])
        || !Hash::check($validated['code'], $verification['code'])
    ) {
        return response()->json([
            'message' => 'The verification code is invalid or expired.',
        ], 422);
    }

    session()->put('seller_registration_verification.verified', true);

    return response()->json([
        'message' => 'Email verified successfully.',
    ]);
})->middleware('guest')->name('seller.register.verify-code');


/*
|--------------------------------------------------------------------------
| SELLER FINAL SUBMIT
| POST
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/register/complete',
    function (Request $request) {

        $sellerData = session(
            'seller_registration',
            []
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure registration exists
        |--------------------------------------------------------------------------
        */

        if (empty($sellerData)) {

            return redirect()
                ->route(
                    'seller.register'
                )
                ->with(
                    'error',
                    'Your seller registration session has expired. Please start again.'
                );

        }

        $sellerEmail = $sellerData['email'] ?? '';
        $hasRejectedRegistration = Registration::where('email', $sellerEmail)
            ->where('status', 'rejected')
            ->exists();

        if (User::where('email', $sellerEmail)
            ->where('registration_status', '!=', 'rejected')
            ->exists() && ! $hasRejectedRegistration) {
            return redirect()
                ->route('seller.register')
                ->with('error', 'This email address is already registered. Please use another email address or log in.');
        }

        $verification = session('seller_registration_verification');

        if (
            !$verification
            || ($verification['email'] ?? null) !== ($sellerData['email'] ?? null)
            || empty($verification['verified'])
        ) {
            return redirect()
                ->route('seller.review.register')
                ->with('error', 'Please verify your email address before submitting your registration.');
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
                'business_permit',
            ]);


        $sellerData =
            array_merge(
                $sellerData,
                $finalData
            );


        app(AuthController::class)->completeSellerRegistration($sellerData);


        /*
        |--------------------------------------------------------------------------
        | Clear Session
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'seller_registration'
        );

        session()->forget(
            'seller_registration_verification'
        );


        /*
        |--------------------------------------------------------------------------
        | Return Login
        |--------------------------------------------------------------------------
        */

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

    $sellerData = session(
        'seller_registration',
        []
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Valid ID
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Delete Business Permit
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Delete Registration Folder
    |--------------------------------------------------------------------------
    */

    if (
        !empty(
            $sellerData['draft_id']
        )
    ) {

        Storage::disk('public')->deleteDirectory(
            "seller-registration/{$sellerData['draft_id']}"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Clear Session
    |--------------------------------------------------------------------------
    */

    session()->forget(
        'seller_registration'
    );


    return redirect()->route(
        'login'
    );

})->middleware('guest')
  ->name('seller.register.exit');


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| BUYER REGISTRATION
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| STEP 1 = Buyer Information
| STEP 2 = Review Information
|
*/


/*
|--------------------------------------------------------------------------
| BUYER STEP 1
| GET
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
| BUYER STEP 1
| POST
|--------------------------------------------------------------------------
*/

Route::post('/buyer/register', function (
    Request $request
) {

    $buyerData = session(
        'buyer_registration',
        []
    );


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


    $buyerData =
        array_merge(
            $buyerData,
            $stepOneData
        );


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
                $buyerData['valid_id_path']
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
    | Go To Buyer Review
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'buyer.review.register'
    );

})->middleware('guest')
  ->name('buyer.register.submit');


/*
|--------------------------------------------------------------------------
| BUYER STEP 2
| GET
|--------------------------------------------------------------------------
*/

Route::get('/buyer/register/review', function () {

    $buyerData = session(
        'buyer_registration',
        []
    );


    /*
    |--------------------------------------------------------------------------
    | Valid ID URL
    |--------------------------------------------------------------------------
    */

    $validIdUrl = null;


    if (
        !empty(
            $buyerData['valid_id_path']
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

    $buyerData = session('buyer_registration', []);
    $email = $buyerData['email'] ?? null;

    if (!$email) {
        return response()->json([
            'message' => 'Your buyer registration session has expired. Please start again.',
        ], 422);
    }

    $previousVerification = session('buyer_registration_verification');

    if (
        $previousVerification
        && !empty($previousVerification['resend_available_at'])
        && now()->lessThan($previousVerification['resend_available_at'])
    ) {
        return response()->json([
            'retry_after' => now()->diffInSeconds($previousVerification['resend_available_at']),
        ], 429);
    }

    if (User::where('email', $email)->exists()) {
        return response()->json([
            'message' => 'This email address is already registered. Please use another email address or log in.',
        ], 422);
    }

    $code = (string) random_int(100000, 999999);
    $isResend = !empty($previousVerification);

    session([
        'buyer_registration_verification' => [
            'email' => $email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'resend_available_at' => now()->addMinutes($isResend ? 10 : 2),
            'verified' => false,
        ],
    ]);

    Mail::to($email)->send(new \App\Mail\BuyerVerificationCode($code));

    return response()->json([
        'message' => 'A verification code has been sent to your email address.',
    ]);
})->middleware('guest')->name('buyer.register.send-code');


Route::post('/buyer/register/verify-code', function (Request $request) {

    $validated = $request->validate([
        'code' => ['required', 'digits:6'],
    ]);

    $verification = session('buyer_registration_verification');

    if (
        !$verification
        || now()->greaterThan($verification['expires_at'])
        || !Hash::check($validated['code'], $verification['code'])
    ) {
        return response()->json([
            'message' => 'The verification code is invalid or expired.',
        ], 422);
    }

    session()->put('buyer_registration_verification.verified', true);

    return response()->json([
        'message' => 'Email verified successfully.',
    ]);
})->middleware('guest')->name('buyer.register.verify-code');


/*
|--------------------------------------------------------------------------
| BUYER FINAL SUBMIT
| POST
|--------------------------------------------------------------------------
*/

Route::post(
    '/buyer/register/complete',
    function (Request $request) {

        $buyerData = session(
            'buyer_registration',
            []
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure registration exists
        |--------------------------------------------------------------------------
        */

        if (empty($buyerData)) {

            return redirect()
                ->route(
                    'buyer.register'
                )
                ->with(
                    'error',
                    'Your buyer registration session has expired. Please start again.'
                );

        }


        $verification = session('buyer_registration_verification');

        if (
            !$verification
            || ($verification['email'] ?? null) !== ($buyerData['email'] ?? null)
            || empty($verification['verified'])
        ) {
            return redirect()
                ->route('buyer.review.register')
                ->with('error', 'Please verify your email address before submitting your registration.');
        }


        /*
        |--------------------------------------------------------------------------
        | Final Submitted Data
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


        app(AuthController::class)->completeBuyerRegistration($buyerData);


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
        | Return To Login
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

    $buyerData = session(
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
            $buyerData['valid_id_path']
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
            $buyerData['draft_id']
        )
    ) {

        Storage::disk('public')->deleteDirectory(
            "buyer-registration/{$buyerData['draft_id']}"
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Clear Session
    |--------------------------------------------------------------------------
    */

    session()->forget(
        'buyer_registration'
    );


    /*
    |--------------------------------------------------------------------------
    | Return To Login
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'login'
    );

})->middleware('guest')
  ->name('buyer.register.exit');
