<?php

namespace App\Http\Controllers\Logistics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class LogisticsRegistrationController extends Controller
{
    public function companyPage()
    {
        return view('pages.logistics landing page.signup-firstpage');
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
            'representative_name' => ['required','string','max:255'],
            'representative_position' => ['required','string','max:255'],
            'representative_email' => ['required','email','max:255'],
            'representative_phone' => ['required','string','max:30'],
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
            $required = in_array($key, ['business_registration','mayors_permit','bir_certificate'])
                && empty($previous[$key]['path']);
            $rules[$key] = [$required ? 'required' : 'nullable', 'file',
                'mimes:jpg,jpeg,png,webp,pdf','max:10240'];
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
        // Step 3 is intentionally not implemented yet. Avoid a 404 or fake submission.
        if (\Illuminate\Support\Facades\Route::has('logistics.register.review')) {
            return redirect()->route('logistics.register.review');
        }
        return redirect()->route('logistics.register.permits')
            ->with('status', 'Your permits and certificates are saved. Step 3 (Review Information) will be connected next.');
    }

    /** Stepper navigation: read saved data only, no validation or writes. */
    public function reviewPage(Request $request)
    {
        return view('pages.logistics landing page.signup-review', [
            'companyData' => $request->session()->get('logistics_registration.company', []),
            'savedDocuments' => $request->session()->get('logistics_registration.documents', []),
        ]);
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
