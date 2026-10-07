<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Registration;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogisticsManagementController extends Controller
{
    public function page()
    {
        $data = $this->clientData();

        return view('pages.admin.logistics-management', [
            'pendingLogisticsRequests' => $data['pendingRequests'],
            'pendingLogisticsTotalEntries' => $data['pendingTotalEntries'],
            'logisticsCompanies' => $data['companies'],
            'rejectedLogisticsArchive' => $data['rejectedArchive'],
            'logisticsTotalEntries' => $data['totalEntries'],
            'logisticsStats' => $data['stats'],
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json($this->clientData());
    }

    public function approve(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        abort_unless($registration->user_type === User::ROLE_LOGISTICS, 404);

        return app(RegistrationController::class)->approve($request, $registration);
    }

    public function reject(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        abort_unless($registration->user_type === User::ROLE_LOGISTICS, 404);

        return app(RegistrationController::class)->reject($request, $registration);
    }

    public function branches(Logistics $logistics): JsonResponse
    {
        abort_unless($logistics->registration_status === 'active', 404);

        return response()->json([
            'data' => $logistics->branches()
                ->withCount('riders')
                ->orderBy('name')
                ->get()
                ->map(fn (LogisticsBranch $branch): array => $this->serializeBranch($branch)),
        ]);
    }

    public function storeBranch(Request $request, Logistics $logistics): JsonResponse
    {
        abort_unless($logistics->registration_status === 'active', 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $branch = $logistics->branches()->create($data)->loadCount('riders');

        return response()->json(['data' => $this->serializeBranch($branch)], 201);
    }

    public function updateBranch(Request $request, LogisticsBranch $branch): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'address' => ['sometimes', 'required', 'string', 'max:500'],
            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
        ]);
        $branch->update($data);

        return response()->json([
            'data' => $this->serializeBranch($branch->loadCount('riders')),
        ]);
    }

    public function destroyBranch(LogisticsBranch $branch): JsonResponse
    {
        $branch->delete();

        return response()->json(['message' => 'Logistics branch deleted.']);
    }

    private function clientData(): array
    {
        $pending = Registration::query()
            ->where('user_type', User::ROLE_LOGISTICS)
            ->where('status', 'pending')
            ->with('user.logisticsProfile')
            ->latest()
            ->get()
            ->map(fn (Registration $registration): array => $this->serializeRegistration($registration));

        $logistics = Logistics::query()
            ->where('registration_status', 'active')
            ->with(['user:id,name,email,created_at', 'branches.riders'])
            ->orderBy('business_name')
            ->get();

        $companies = $logistics
            ->map(fn (Logistics $company): array => $this->serializeCompany($company));

        $rejected = Registration::query()
            ->where('user_type', User::ROLE_LOGISTICS)
            ->where('status', 'rejected')
            ->with('user.logisticsProfile')
            ->latest('reviewed_at')
            ->get()
            ->map(fn (Registration $registration): array => $this->serializeRejected($registration));

        return [
            'pendingRequests' => $pending->values(),
            'pendingTotalEntries' => $pending->count(),
            'companies' => $companies->values(),
            'rejectedArchive' => $rejected->values(),
            'totalEntries' => $companies->count(),
            'stats' => [
                'pending_requests' => $pending->count(),
                'accepted_logistics' => $companies->count(),
                'rejected_logistics' => $rejected->count(),
                'total_companies' => $companies->count(),
            ],
        ];
    }

    private function serializeRegistration(Registration $registration): array
    {
        $profile = $registration->user?->logisticsProfile;
        $businessPermit = $registration->business_permit_path ?: $profile?->upload_business_permit;

        return [
            'id' => $registration->id,
            'company' => $registration->business_name
                ?: $profile?->business_name
                ?: $registration->full_name,
            'email' => $registration->email,
            'phone' => $registration->phone,
            'contact_person' => $registration->full_name,
            'registered_date' => $registration->created_at?->toDateString(),
            'registered_display' => $registration->created_at?->format('M d, Y g:i A'),
            'office_address' => $this->address($profile, $registration),
            'description' => '',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => $businessPermit
                ? Storage::disk('public')->url($businessPermit)
                : '',
        ];
    }

    private function serializeCompany(Logistics $logistics): array
    {
        $branches = $logistics->branches;

        return [
            'id' => $logistics->id,
            'company' => $logistics->business_name ?: $logistics->user?->name ?: 'Logistics Company',
            'branches' => $branches->count(),
            'riders' => $branches->sum(fn (LogisticsBranch $branch): int => $branch->riders->count()),
            'contact_number' => $logistics->contact_no ?: '—',
            'company_email' => $logistics->user?->email ?: '—',
            'office_address' => $this->address($logistics),
            'description' => '',
            'dti_permit' => '',
            'business_permit' => $logistics->upload_business_permit
                ? Storage::disk('public')->url($logistics->upload_business_permit)
                : '',
            'partnership_since' => $logistics->approved_at?->year ?? $logistics->created_at?->year,
            'account_created_date' => $logistics->created_at?->format('F d, Y'),
            'account_created_time' => $logistics->created_at?->format('g:i A'),
            'logo' => '',
            'branches_data' => $branches->map(fn (LogisticsBranch $branch): array => [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address,
                'contact_person' => $branch->contact_person,
                'phone' => $branch->phone,
                'riders' => $branch->riders->count(),
            ])->values(),
        ];
    }

    private function serializeRejected(Registration $registration): array
    {
        $item = $this->serializeRegistration($registration);

        return [
            'company' => $item['company'],
            'email' => $item['email'],
            'phone' => $item['phone'],
            'contact_person' => $item['contact_person'],
            'rejected_display' => $registration->reviewed_at?->format('M d, Y'),
            'rejection_reason' => $registration->rejection_reason,
            'rejection_details' => $registration->rejection_details,
        ];
    }

    private function address(?object $profile, ?Registration $registration = null): string
    {
        return collect([
            $profile?->house_number ?? $registration?->house_no,
            $profile?->street ?? $registration?->street,
            $profile?->barangay ?? $registration?->barangay,
            $profile?->municipality ?? $registration?->municipality,
            $profile?->province ?? $registration?->province,
        ])->filter()->implode(', ') ?: '—';
    }

    private function serializeBranch(LogisticsBranch $branch): array
    {
        return [
            'id' => $branch->id,
            'name' => $branch->name,
            'address' => $branch->address,
            'contact_person' => $branch->contact_person,
            'phone' => $branch->phone,
            'riders' => $branch->riders_count,
        ];
    }
}
