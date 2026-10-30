<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourierRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sorting_center_id' => ['required', 'integer', Rule::exists('logistics_branches', 'id')],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'sex' => ['nullable', 'in:Male,Female,Other,male,female,other'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'region' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'house_no' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'vehicle' => ['required', 'string', 'max:80'],
            'plate_number' => ['required', 'string', 'max:50'],
            'or_cr' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'drivers_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
        $center = LogisticsBranch::query()->whereKey($data['sorting_center_id'])
            ->whereHas('user', fn ($query) => $query->where('role', User::ROLE_SORTING_CENTER))->first();
        if (! $center) {
            throw ValidationException::withMessages(['sorting_center_id' => 'Choose a valid sorting center.']);
        }
        if (CourierRegistration::query()->where('sorting_center_id', $center->id)->where('email', $data['email'])->where('status', 'pending')->exists()) {
            throw ValidationException::withMessages(['email' => 'A pending application already exists for this email and sorting center.']);
        }

        $data['or_cr_path'] = isset($data['or_cr']) ? $data['or_cr']->store('courier-registrations/or-cr', 'public') : null;
        $data['drivers_license_path'] = isset($data['drivers_license']) ? $data['drivers_license']->store('courier-registrations/drivers-license', 'public') : null;
        unset($data['or_cr'], $data['drivers_license']);
        $data['status'] = 'pending';
        $registration = CourierRegistration::query()->create($data);

        return response()->json(['message' => 'Courier registration submitted for review.', 'data' => $registration], 201);
    }

    private function center(Request $request): LogisticsBranch
    {
        return LogisticsBranch::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $query = CourierRegistration::query()->where('sorting_center_id', $center->id)->latest('created_at');
        if ($request->filled('status') && $request->string('status')->toString() !== 'all') {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }
        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->toString().'%';
            $query->where(fn ($q) => $q->where('first_name', 'like', $term)->orWhere('middle_name', 'like', $term)
                ->orWhere('last_name', 'like', $term)->orWhere('email', 'like', $term)
                ->orWhere('phone', 'like', $term)->orWhere('plate_number', 'like', $term));
        }
        return response()->json(['data' => $query->get()]);
    }

    public function show(Request $request, CourierRegistration $registration): JsonResponse
    {
        abort_unless($registration->sorting_center_id === $this->center($request)->id, 404);
        return response()->json(['data' => $registration]);
    }

    public function decide(Request $request, CourierRegistration $registration): JsonResponse
    {
        abort_unless($registration->sorting_center_id === $this->center($request)->id, 404);
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:500'],
        ]);

        $registration = DB::transaction(function () use ($request, $registration, $data): CourierRegistration {
            $locked = CourierRegistration::query()->whereKey($registration->id)->lockForUpdate()->firstOrFail();
            if ($locked->sorting_center_id !== $this->center($request)->id) {
                abort(404);
            }
            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Only pending registrations can be reviewed.']);
            }
            $locked->update([
                'status' => $data['status'],
                'rejection_reason' => $data['status'] === 'rejected' ? trim($data['reason']) : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
            return $locked->refresh();
        });

        return response()->json(['message' => 'Courier registration '.$registration->status.'.', 'data' => $registration]);
    }
}
