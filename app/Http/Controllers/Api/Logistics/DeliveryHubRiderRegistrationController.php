<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Http\Services\DeliveryHubRiderRegistrationService;
use App\Models\Admin\Registration;
use App\Models\Logistics\DeliveryHubRiderRegistration;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DeliveryHubRiderRegistrationController extends Controller
{
    public function available(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province' => ['required', 'string', 'max:120'],
            'municipality' => ['nullable', 'string', 'max:120'],
        ]);
        $province = mb_strtolower(trim($data['province']));
        $hubs = LogisticsDeliveryHub::query()
            ->where('status', 'active')->whereNull('archived_at')
            ->whereRaw('LOWER(province) = ?', [$province])
            ->when(! empty($data['municipality']), fn (Builder $query) => $query
                ->whereRaw('LOWER(municipality) = ?', [mb_strtolower(trim($data['municipality']))]))
            ->whereHas('logisticsBranch', fn (Builder $branch) => $branch
                ->where('status', 'active')->whereNull('archived_at')
                ->whereHas('user', fn (Builder $user) => $user
                    ->where('role', User::ROLE_SORTING_CENTER)
                    ->where('registration_status', 'active'))
                ->whereHas('logistics', fn (Builder $company) => $company
                    ->where('registration_status', 'active')
                    ->whereHas('user', fn (Builder $user) => $user
                        ->where('role', User::ROLE_LOGISTICS)
                        ->where('registration_status', 'active'))))
            ->whereHas('user', fn (Builder $user) => $user
                ->where('role', User::ROLE_DELIVERY_HUB)
                ->where('registration_status', 'active'))
            ->with('logisticsBranch.logistics')
            ->orderBy('hub_name')
            ->get()
            ->map(fn (LogisticsDeliveryHub $hub): array => [
                'id' => $hub->id,
                'name' => $hub->hub_name,
                'province' => $hub->province,
                'municipality' => $hub->municipality,
                'city' => $hub->municipality,
                'logistics_company' => $hub->logisticsBranch?->logistics?->business_name,
            ]);

        return response()->json(['data' => $hubs->values()]);
    }

    public function index(Request $request, DeliveryHubRiderRegistrationService $service): JsonResponse
    {
        $hub = $this->hub($request);
        $query = DeliveryHubRiderRegistration::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->with(['user.riderProfile', 'deliveryHub.logisticsBranch.logistics']);

        if ($request->filled('status') && in_array($request->string('status')->toString(), ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }
        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->toString().'%';
            $query->whereHas('user', fn (Builder $user) => $user
                ->where('email', 'like', $term)
                ->orWhereHas('riderProfile', fn (Builder $rider) => $rider
                    ->where('first_name', 'like', $term)
                    ->orWhere('middle_initial', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('contact_no', 'like', $term)
                    ->orWhere('plate_number', 'like', $term)));
        }

        return response()->json([
            'data' => $query->latest('created_at')->get()
                ->map(fn (DeliveryHubRiderRegistration $application): array => $service->present($application))
                ->values(),
        ]);
    }

    public function show(Request $request, DeliveryHubRiderRegistration $registration, DeliveryHubRiderRegistrationService $service): JsonResponse
    {
        abort_unless($registration->logistics_delivery_hub_id === $this->hub($request)->id, 404);

        return response()->json(['data' => $service->present($registration)]);
    }

    public function decide(Request $request, DeliveryHubRiderRegistration $registration): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless($registration->logistics_delivery_hub_id === $hub->id, 404);
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:500'],
        ]);

        $application = DB::transaction(function () use ($request, $hub, $registration, $data): DeliveryHubRiderRegistration {
            $locked = DeliveryHubRiderRegistration::query()->whereKey($registration->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->logistics_delivery_hub_id === $hub->id, 404);
            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Only pending rider registrations can be reviewed.']);
            }

            $user = User::query()->whereKey($locked->user_id)->lockForUpdate()->firstOrFail();
            $rider = Rider::query()->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $approved = $data['status'] === 'approved';
            $now = now();

            $user->forceFill([
                'registration_status' => $approved ? 'active' : 'rejected',
                'approved_at' => $approved ? $now : null,
                'rejected_at' => $approved ? null : $now,
            ])->save();
            $rider->forceFill([
                'logistics_branch_id' => $hub->logistics_branch_id,
                'municipal_hub' => $hub->hub_name,
                'assignment_province' => $hub->province,
                'registration_status' => $approved ? 'active' : 'rejected',
                'approved_at' => $approved ? $now : null,
                'rejected_at' => $approved ? null : $now,
            ])->save();

            Registration::query()->where('user_id', $user->id)
                ->where('user_type', User::ROLE_RIDER)
                ->latest('id')->first()?->update([
                    'status' => $data['status'],
                    'rejection_reason' => $approved ? null : mb_substr(trim($data['reason']), 0, 255),
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => $now,
                ]);

            $locked->update([
                'status' => $data['status'],
                'rejection_reason' => $approved ? null : trim($data['reason']),
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => $now,
            ]);

            return $locked->refresh()->load(['user.riderProfile', 'deliveryHub.logisticsBranch.logistics']);
        });

        return response()->json([
            'message' => $application->status === 'approved'
                ? 'Rider registration approved. The rider can now log in with their mobile account.'
                : 'Rider registration rejected.',
            'data' => app(DeliveryHubRiderRegistrationService::class)->present($application),
        ]);
    }

    public function assign(Request $request, DeliveryHubRiderRegistration $registration): JsonResponse
    {
        $hub = $this->hub($request);
        abort_unless((int) $registration->logistics_delivery_hub_id === (int) $hub->id, 404);
        abort_unless($registration->status === 'approved', 409, 'Only approved riders can be assigned.');

        $data = $request->validate([
            'role' => ['required', 'in:pickup,delivery'],
            'barangay' => ['required', 'string', 'max:255'],
        ]);
        $barangay = trim($data['barangay']);
        $rider = $registration->user?->riderProfile;
        abort_unless($rider, 404);

        $conflict = DeliveryHubRiderRegistration::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->where('status', 'approved')
            ->where('id', '!=', $registration->id)
            ->with('user.riderProfile')
            ->get()
            ->contains(function (DeliveryHubRiderRegistration $other) use ($data, $barangay): bool {
                $profile = $other->user?->riderProfile;
                if (! $profile) return false;
                $operation = mb_strtolower((string) $profile->logistics_operation);
                $sameRole = str_contains($operation, $data['role']);
                $sameBarangay = mb_strtolower(trim((string) ($profile->assignment_barangay ?: $profile->barangay)))
                    === mb_strtolower($barangay);

                return $sameRole && $sameBarangay;
            });

        if ($conflict) {
            throw ValidationException::withMessages([
                'barangay' => 'This barangay already has an assigned '.($data['role'] === 'pickup' ? 'pickup' : 'delivery').' rider.',
            ]);
        }

        $rider->forceFill([
            'logistics_operation' => ucfirst($data['role']),
            'assignment_barangay' => $barangay,
        ])->save();

        return response()->json([
            'message' => 'Rider duty assignment saved.',
            'data' => [
                'registration_id' => $registration->id,
                'role' => $data['role'],
                'barangay' => $barangay,
            ],
        ]);
    }

    public function page(Request $request, DeliveryHubRiderRegistrationService $service): View
    {
        $hub = $this->hub($request);
        $registrations = DeliveryHubRiderRegistration::query()
            ->where('logistics_delivery_hub_id', $hub->id)
            ->with(['user.riderProfile', 'deliveryHub.logisticsBranch.logistics'])
            ->latest('created_at')->get()
            ->map(fn (DeliveryHubRiderRegistration $application): array => $service->present($application));

        return view('pages.logistics.delivery-hub.rider-registrations', [
            'hub' => $hub,
            'deliveryHub' => $hub,
            'deliveryHubId' => $hub->id,
            'riderRegistrations' => $registrations->values(),
        ]);
    }

    private function hub(Request $request): LogisticsDeliveryHub
    {
        return LogisticsDeliveryHub::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('archived_at')
            ->firstOrFail();
    }
}
