<?php

namespace App\Http\Controllers\Logistics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\RegistrationService;
use App\Mail\LogisticsVerificationCode;
use App\Models\Admin\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class LogisticsRegistrationController extends Controller
{
    public function companyPage()
    {
        return view('pages.logistics landing page.signup-firstpage', [
            'companyData' => request()->session()->get('logistics_registration.company', []),
        ]);
    }

    public function saveCompany(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required','string','max:255'],
            'registered_business_name' => ['required','string','max:255'],
            'company_type' => ['required','string','max:100'],
            'years_in_operation' => ['nullable','integer','min:0'],
            'company_email' => ['required','email','max:255'],
            'company_phone' => ['required','string','max:30'],
            'website' => ['nullable','url','max:255'],
            'branch_count' => ['nullable','integer','min:0'],
            'representative_name' => ['required','string','max:255','regex:/^\S+\s+\S+/u'],
            'representative_position' => ['required','string','max:255'],
            'representative_email' => ['required','email','max:255'],
            'representative_phone' => ['required','string','max:30'],
            'representative_sex' => ['required','in:male,female,other'],
            'representative_birthday' => ['required','date','before_or_equal:'.now()->subYears(18)->toDateString()],
            'region' => ['required','string','max:120'],
            'province' => ['required','string','max:150'],
            'city' => ['required','string','max:150'],
            'barangay' => ['required','string','max:150'],
            'postal_code' => ['required','regex:/^[0-9]{4}$/'],
            'street_address' => ['nullable','string','max:500'],
            'street' => ['nullable','string','max:255'],
            'house_no' => ['nullable','string','max:80'],
            'building' => ['nullable','string','max:255'],
            'subdivision' => ['nullable','string','max:255'],
            'password' => ['required','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()],
            'accept_terms' => ['accepted'],
        ]);
        // Never put a raw password into a session or file. Encrypt only until
        // Step 3 creates the actual user and securely hashes it using Hash::make.
        $password = $data['password'];
        unset($data['password']);
        $request->session()->put('logistics_registration.company', $data);
        $request->session()->put('logistics_registration.encrypted_password', Crypt::encryptString($password));
        return redirect()->route('logistics.register.permits');
    }

    public function permitsPage(Request $request)
    {
        // Navigation is allowed even if Step 1 was not saved.
        // Unsaved Step 1 inputs are never sent to this endpoint.
        return view('pages.logistics landing page.signup-secpage');
    }

    public function savePermits(Request $request)
    {
        $keys = ['business_registration','mayors_permit','bir_certificate',
            'courier_authority','insurance_certificate','representative_authorization'];
        $previous = $request->session()->get('logistics_registration.documents', []);
        if (!is_array($previous)) $previous = [];
        $rules=[];
        foreach ($keys as $key) {
            $required = in_array($key, [
                'business_registration',
                'mayors_permit',
                'bir_certificate',
                'representative_authorization',
            ], true)
                && empty($previous[$key]['path']);
            $rules[$key] = [$required ? 'required' : 'nullable', 'file',
                'mimes:jpg,jpeg,png,webp,pdf','max:5120'];
        }
        $request->validate($rules);
        $documents = $previous;
        foreach ($keys as $key) {
            if (!$request->hasFile($key)) continue;
            $file = $request->file($key);
            // Local/private storage, not public URLs.
            $path = $file->store('logistics-registrations/temporary', 'local');
            $old = $documents[$key]['path'] ?? null;
            $documents[$key] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
            if ($old && $old !== $path) Storage::disk('local')->delete($old);
        }
        $request->session()->put('logistics_registration.documents', $documents);
        return redirect()->route('logistics.register.review');
    }

    /** Stepper navigation: read saved data only, no validation or writes. */
    public function reviewPage(Request $request)
    {
        return view('pages.logistics landing page.signup-review', [
            'companyData' => $request->session()->get('logistics_registration.company', []),
            'savedDocuments' => $request->session()->get('logistics_registration.documents', []),
        ]);
    }

    public function sendVerificationCode(Request $request)
    {
        $company = $request->session()->get('logistics_registration.company');
        $email = is_array($company) ? ($company['company_email'] ?? null) : null;
        if (!is_string($email) || $email === '') {
            return response()->json([
                'message' => 'Your logistics registration session has expired. Please start again.',
            ], 422);
        }

        $previousVerification = $request->session()->get('logistics_registration.verification');
        if (is_array($previousVerification)
            && !empty($previousVerification['resend_available_at'])
            && now()->lt($previousVerification['resend_available_at'])) {
            return response()->json([
                'message' => 'Please wait before requesting another code. Check your inbox for the latest code.',
                'retry_after' => now()->diffInSeconds($previousVerification['resend_available_at']),
            ], 429);
        }

        $hasRejectedRegistration = Registration::query()
            ->where('email', $email)
            ->where('status', 'rejected')
            ->exists();
        if (User::query()
            ->where('email', $email)
            ->where('registration_status', '!=', 'rejected')
            ->exists() && !$hasRejectedRegistration) {
            return response()->json([
                'message' => 'This email address is already registered. Please use another email address or log in.',
            ], 422);
        }

        $code = (string) random_int(100000, 999999);
        Mail::to($email)->send(new LogisticsVerificationCode($code));

        $request->session()->put('logistics_registration.verification', [
            'email' => $email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'resend_available_at' => now()->addSeconds(60),
            'verified' => false,
        ]);

        return response()->json([
            'message' => 'A verification code has been sent to your company email address.',
            'retry_after' => 60,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);
        $company = $request->session()->get('logistics_registration.company');
        $verification = $request->session()->get('logistics_registration.verification');
        $email = is_array($company) ? ($company['company_email'] ?? null) : null;

        if (!is_array($verification)
            || !is_string($email)
            || ($verification['email'] ?? null) !== $email
            || now()->greaterThan($verification['expires_at'])
            || !Hash::check($validated['code'], $verification['code'])) {
            return response()->json([
                'message' => 'The verification code is invalid or expired. Request a new code and try again.',
            ], 422);
        }

        $request->session()->put('logistics_registration.verification.verified', true);

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    public function completeRegistration(Request $request, RegistrationService $registrationService)
    {
        $company = $request->session()->get('logistics_registration.company');
        $documents = $request->session()->get('logistics_registration.documents');
        $encryptedPassword = $request->session()->get('logistics_registration.encrypted_password');
        $verification = $request->session()->get('logistics_registration.verification');

        if (!is_array($company) || !is_array($documents) || !is_string($encryptedPassword)) {
            return redirect()->route('logistics.register.company')
                ->withErrors(['registration' => 'Please complete the company details and document uploads first.']);
        }

        $companyValidator = validator($company, [
            'company_name' => ['required', 'string', 'max:255'],
            'registered_business_name' => ['required', 'string', 'max:255'],
            'company_type' => ['required', 'string', 'max:100'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['required', 'string', 'max:30'],
            'representative_name' => ['required', 'string', 'max:255', 'regex:/^\S+\s+\S+/u'],
            'representative_position' => ['required', 'string', 'max:255'],
            'representative_email' => ['required', 'email', 'max:255'],
            'representative_phone' => ['required', 'string', 'max:30'],
            'representative_sex' => ['required', 'in:male,female,other'],
            'representative_birthday' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'province' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:150'],
            'barangay' => ['required', 'string', 'max:150'],
            'postal_code' => ['required', 'regex:/^[0-9]{4}$/'],
        ]);
        if ($companyValidator->fails()) {
            return redirect()->route('logistics.register.company')
                ->withErrors($companyValidator)
                ->withInput($company);
        }

        if (!is_array($verification)
            || ($verification['email'] ?? null) !== ($company['company_email'] ?? null)
            || empty($verification['verified'])
            || now()->greaterThan($verification['expires_at'])) {
            return redirect()->route('logistics.register.review')
                ->withErrors(['verification' => 'Verify your company email before submitting your registration.']);
        }

        $requiredDocuments = [
            'business_registration',
            'mayors_permit',
            'bir_certificate',
            'representative_authorization',
        ];
        foreach ($requiredDocuments as $key) {
            $path = $documents[$key]['path'] ?? null;
            if (!is_string($path)
                || !str_starts_with($path, 'logistics-registrations/temporary/')
                || !Storage::disk('local')->exists($path)) {
                return redirect()->route('logistics.register.permits')
                    ->withErrors(['registration' => 'Please upload and save all required company documents.']);
            }
        }

        $publicDocuments = [];
        foreach ($documents as $key => $entry) {
            $temporaryPath = $entry['path'] ?? null;
            if (!is_string($temporaryPath)
                || !str_starts_with($temporaryPath, 'logistics-registrations/temporary/')
                || !Storage::disk('local')->exists($temporaryPath)) {
                continue;
            }

            $extension = match ($entry['mime'] ?? null) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                default => null,
            };
            if ($extension === null) {
                return redirect()->route('logistics.register.permits')
                    ->withErrors(['registration' => 'A saved document has an unsupported file type. Please upload it again.']);
            }

            $publicPath = 'registrations/logistics/'.Str::uuid().'.'.$extension;
            if (!Storage::disk('public')->put(
                $publicPath,
                Storage::disk('local')->get($temporaryPath)
            )) {
                throw new RuntimeException('Unable to store a logistics registration document.');
            }

            $publicDocuments[$key] = [
                'path' => $publicPath,
                'original_name' => $entry['original_name'] ?? 'document.'.$extension,
                'mime' => $entry['mime'],
                'size' => $entry['size'] ?? null,
            ];
        }

        $nameParts = preg_split('/\s+/', trim($company['representative_name']), -1, PREG_SPLIT_NO_EMPTY);
        $firstName = array_shift($nameParts);
        $lastName = implode(' ', $nameParts);
        $validIdPath = $publicDocuments['representative_authorization']['path'];
        $businessPermitPath = $publicDocuments['mayors_permit']['path'];

        $registrationService->completeLogisticsRegistration([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'sex' => $company['representative_sex'],
            'email' => $company['company_email'],
            'contact_no' => $company['company_phone'],
            'birthday' => $company['representative_birthday'],
            'province' => $company['province'],
            'municipality' => $company['city'],
            'barangay' => $company['barangay'],
            'street' => ($company['street_address'] ?? '') ?: (($company['street'] ?? '') ?: 'Not provided'),
            'house_number' => ($company['house_no'] ?? '') ?: 'Not provided',
            'zip_code' => $company['postal_code'],
            'business_name' => $company['company_name'],
            'business_category' => $company['company_type'],
            'password' => Crypt::decryptString($encryptedPassword),
            'password_confirmation' => Crypt::decryptString($encryptedPassword),
            'valid_id_path' => $validIdPath,
            'business_permit_path' => $businessPermitPath,
            'registration_details' => [
                'company' => $company,
                'documents' => $publicDocuments,
            ],
        ]);

        foreach ($documents as $entry) {
            $path = $entry['path'] ?? null;
            if (is_string($path) && str_starts_with($path, 'logistics-registrations/temporary/')) {
                Storage::disk('local')->delete($path);
            }
        }

        $request->session()->forget([
            'logistics_registration.company',
            'logistics_registration.documents',
            'logistics_registration.encrypted_password',
            'logistics_registration.verification',
        ]);

        return redirect()->route('logistics.landing.info')
            ->with('success', 'Your logistics registration has been submitted. It is now waiting for administrator approval.');
    }

    /** Only serve files referenced by this visitor's temporary registration session. */
    public function reviewDocument(Request $request, string $type)
    {
        $allowed = ['business_registration', 'mayors_permit', 'bir_certificate',
            'courier_authority', 'insurance_certificate', 'representative_authorization'];
        abort_unless(in_array($type, $allowed, true), 404);
        $entry = $request->session()->get('logistics_registration.documents.' . $type);
        abort_unless(is_array($entry) && !empty($entry['path']), 404);
        $path = (string) $entry['path'];
        abort_unless(str_starts_with($path, 'logistics-registrations/temporary/')
            && Storage::disk('local')->exists($path), 404);
        $mime = $entry['mime'] ?? 'application/octet-stream';
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true), 404);
        return Storage::disk('local')->response($path, $entry['original_name'] ?? 'document', [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
