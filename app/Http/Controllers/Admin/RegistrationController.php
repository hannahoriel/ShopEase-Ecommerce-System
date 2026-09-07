<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use App\Models\Admin\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    /**
     * The reasons offered on the reject-registration form.
     * Kept in one place so the controller and the request validation
     * always match what the modal on the frontend can submit.
     */
    private const REJECT_REASONS = [
        'Incomplete or missing required documents.',
        'Invalid or unverifiable information.',
        'Does not meet platform requirements.',
        'Prohibited or restricted business/category.',
        'Duplicate account.',
        'Other (please specify)',
    ];

    /**
     * Admin: Account Registrations page (pending list + stat cards).
     */
    public function index(Request $request)
    {
        $query = Registration::pending();
        $this->applyFilters($query, $request);

        $registrations = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return view('pages.admin.registrations', [
            'registrations' => $registrations,
            'counts' => $this->counts(),
        ]);
    }

    /**
     * JSON: pending registrations, filtered/searched/paginated.
     * Used by the search box, user-type filter, date filter, and pager.
     */
    public function list(Request $request): JsonResponse
    {
        $query = Registration::pending();
        $this->applyFilters($query, $request);

        $registrations = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json($registrations);
    }

    /**
     * JSON: approved users archive (for the "Approved Users" modal).
     */
    public function approvedArchive(Request $request): JsonResponse
    {
        $query = Registration::approved();

        if ($search = $request->string('search')->trim()->value()) {
            $this->applySearch($query, $search);
        }

        $registrations = $query->orderByDesc('reviewed_at')->get();

        return response()->json([
            'data' => $registrations,
            'count' => $registrations->count(),
        ]);
    }

    /**
     * JSON: rejected users archive (for the "Rejected Users" modal).
     */
    public function rejectedArchive(Request $request): JsonResponse
    {
        $query = Registration::rejected();

        if ($search = $request->string('search')->trim()->value()) {
            $this->applySearch($query, $search);
        }

        $registrations = $query->orderByDesc('reviewed_at')->get();

        return response()->json([
            'data' => $registrations,
            'count' => $registrations->count(),
        ]);
    }

    /**
     * A single registration's full details, for the Seller/Buyer Details modal.
     */
    public function show(Registration $registration): JsonResponse
    {
        return response()->json($registration);
    }

    /**
     * Approve a registration: create the live user account and notify the applicant.
     */
    public function approve(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        if ($registration->status !== 'pending') {
            return response()->json([
                'message' => 'This registration has already been reviewed.',
            ], 409);
        }

        DB::transaction(function () use ($registration, $request) {
            $user = User::create([
                'name' => $registration->full_name,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'role' => $registration->user_type, // 'seller' | 'buyer'
                'password' => $registration->password ?? bcrypt(Str::random(24)),
                'email_verified_at' => now(),
                'registration_status' => 'active',
                'approved_at' => now(),
            ]);

            $registration->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
                'user_id' => $user->id,
            ]);
        });

        Mail::to($registration->email)->queue(new RegistrationApproved($registration));

        $message = "{$registration->full_name}'s registration has been approved. The user has been notified via email.";

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'counts' => $this->counts(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Reject a registration and notify the applicant.
     */
    public function reject(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        if ($registration->status !== 'pending') {
            return response()->json([
                'message' => 'This registration has already been reviewed.',
            ], 409);
        }

        $validated = $request->validate([
            'reason' => ['required', Rule::in(self::REJECT_REASONS)],
            'details' => ['nullable', 'string', 'max:300'],
        ], [
            'reason.required' => 'Please select a reason before rejecting the registration.',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'rejection_details' => $validated['details'] ?? null,
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        Mail::to($registration->email)->queue(new RegistrationRejected($registration));

        $message = "{$registration->full_name}'s registration has been rejected. The user has been notified via email.";

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'counts' => $this->counts(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Apply the search box + user-type filter + date filter to a query.
     */
    private function applyFilters($query, Request $request): void
    {
        if ($search = $request->string('search')->trim()->value()) {
            $this->applySearch($query, $search);
        }

        if ($type = $request->string('user_type')->value()) {
            if (in_array($type, ['seller', 'buyer'], true)) {
                $query->where('user_type', $type);
            }
        }

        if ($date = $request->string('date')->value()) {
            $query->whereDate('created_at', $date);
        }
    }

    /**
     * Search across name, email, and phone — matching the frontend search box.
     */
    private function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
        });
    }

    /**
     * Counts for the Pending / Approved / Rejected / Total stat cards.
     */
    private function counts(): array
    {
        return [
            'pending' => Registration::pending()->count(),
            'approved' => Registration::approved()->count(),
            'rejected' => Registration::rejected()->count(),
            'total' => Registration::count(),
        ];
    }
}
