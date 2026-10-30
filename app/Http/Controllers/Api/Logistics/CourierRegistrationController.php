<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Registration;
use App\Models\Logistics\CourierRegistration;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourierRegistrationController extends Controller
{
    public function centers(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province' => ['required', 'string', 'max:120'],
        ]);
        $province = mb_strtolower(trim($data['province']));
        $centers = LogisticsBranch::query()
            ->whereRaw('LOWER(province) = ?', [$province])
            ->where('status', 'active')
            ->whereNull('archived_at')
            ->whereHas('user', fn ($query) => $query
                ->where('role', User::ROLE_SORTING_CENTER)
                ->where('registration_status', 'active'))
            ->with('logistics:id,business_name')
            ->orderBy('name')
            ->get(['id', 'name', 'province', 'municipality', 'logistics_id'])
            ->map(fn (LogisticsBranch $center): array => [
                'id' => $center->id,
                'name' => $center->name,
                'province' => $center->province,
                'city' => $center->municipality,
                'company' => $center->logistics?->business_name,
            ]);

        return response()->json(['data' => $centers]);
    }

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

    public function syncLegacyMobileApplications(LogisticsBranch $center): void
    {
        $center->loadMissing('logistics');
        $company = trim((string) $center->logistics?->business_name);
        if (! $center->province) {
            return;
        }

        $pendingCouriers = Rider::query()
            ->with('user')
            ->where('registration_type', 'courier')
            ->where('registration_status', 'pending')
            ->whereRaw('LOWER(TRIM(assignment_province)) = ?', [mb_strtolower(trim($center->province))])
            ->whereHas('user', fn ($query) => $query
                ->where('role', User::ROLE_RIDER)
                ->where('registration_status', 'pending'));
        $riders = $company === ''
            ? collect()
            : (clone $pendingCouriers)
                ->whereRaw('LOWER(TRIM(courier_company)) = ?', [mb_strtolower($company)])
                ->get();

        if ($riders->isEmpty()) {
            $centerCount = LogisticsBranch::query()
                ->whereRaw('LOWER(province) = ?', [mb_strtolower(trim($center->province))])
                ->where('status', 'active')->whereNull('archived_at')
                ->whereHas('user', fn ($query) => $query
                    ->where('role', User::ROLE_SORTING_CENTER)
                    ->where('registration_status', 'active'))
                ->count();
            if ($centerCount === 1) {
                $riders = $pendingCouriers->get();
            }
        }

        foreach ($riders as $rider) {
            if (! $rider->user || CourierRegistration::query()->where('user_id', $rider->user_id)->exists()) {
                continue;
            }

            CourierRegistration::query()->create([
                'sorting_center_id' => $center->id,
                'user_id' => $rider->user_id,
                'first_name' => $rider->first_name,
                'middle_name' => $rider->middle_initial,
                'last_name' => $rider->last_name,
                'sex' => $rider->sex,
                'birthday' => $rider->birthday,
                'email' => $rider->user->email,
                'phone' => $rider->contact_no,
                'region' => $rider->assignment_region,
                'province' => $rider->province,
                'city' => $rider->municipality,
                'barangay' => $rider->barangay,
                'street' => $rider->street,
                'house_no' => $rider->house_number,
                'vehicle' => mb_substr($rider->vehicle, 0, 80),
                'plate_number' => $rider->plate_number,
                'or_cr_path' => $rider->upload_or_cr,
                'drivers_license_path' => $rider->upload_id_license,
                'status' => 'pending',
            ]);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $this->syncLegacyMobileApplications($center);
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
            if ($data['status'] === 'approved') {
                $email = mb_strtolower(trim($locked->email));
                $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first();
                if ($user && $user->role !== User::ROLE_RIDER) {
                    throw ValidationException::withMessages([
                        'email' => 'This email is already linked to another account. Resolve that account before approving this courier.',
                    ]);
                }
                $rider = $user?->riderProfile;
                if ($rider && $rider->registration_status !== 'pending') {
                    throw ValidationException::withMessages([
                        'email' => 'This rider account has already been reviewed.',
                    ]);
                }
                if (! $user) {
                    $user = User::query()->create([
                        'name' => trim($locked->first_name.' '.$locked->last_name),
                        'email' => $email,
                        'password' => Str::random(64),
                        'role' => User::ROLE_RIDER,
                        'registration_status' => 'active',
                        'approved_at' => now(),
                    ]);
                } else {
                    $user->update(['registration_status' => 'active', 'approved_at' => now()]);
                }

                $riderData = [
                    'user_id' => $user->id,
                    'logistics_branch_id' => $locked->sorting_center_id,
                    'last_name' => $locked->last_name,
                    'first_name' => $locked->first_name,
                    'middle_initial' => $locked->middle_name,
                    'sex' => strtolower($locked->sex ?: 'other'),
                    'contact_no' => $locked->phone,
                    'birthday' => $locked->birthday ?: '2000-01-01',
                    'age' => $locked->birthday ? $locked->birthday->age : 26,
                    'province' => $locked->province ?: '',
                    'municipality' => $locked->city ?: '',
                    'barangay' => $locked->barangay ?: '',
                    'street' => $locked->street ?: '',
                    'house_number' => $locked->house_no ?: '',
                    'vehicle' => $locked->vehicle,
                    'registration_type' => 'courier',
                    'assignment_region' => $locked->region,
                    'assignment_province' => $locked->province,
                    'plate_number' => $locked->plate_number,
                    'courier_company' => $locked->sortingCenter?->name,
                    'upload_or_cr' => $locked->or_cr_path,
                    'upload_id_license' => $locked->drivers_license_path,
                    'registration_status' => 'active',
                    'approved_at' => now(),
                ];
                if ($rider) {
                    $rider->update($riderData);
                } else {
                    Rider::query()->create($riderData);
                }

                $locked->user_id = $user->id;
            }

            $locked->update([
                'status' => $data['status'],
                'rejection_reason' => $data['status'] === 'rejected' ? trim($data['reason']) : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            if ($locked->user_id) {
                $status = $data['status'] === 'approved' ? 'active' : 'rejected';
                User::query()->whereKey($locked->user_id)->update([
                    'registration_status' => $status,
                    'approved_at' => $status === 'active' ? now() : null,
                    'rejected_at' => $status === 'rejected' ? now() : null,
                ]);
                Rider::query()->where('user_id', $locked->user_id)->update([
                    'registration_status' => $status,
                    'approved_at' => $status === 'active' ? now() : null,
                    'rejected_at' => $status === 'rejected' ? now() : null,
                ]);
                Registration::query()->where('user_id', $locked->user_id)
                    ->where('user_type', User::ROLE_RIDER)
                    ->latest('id')->first()?->update([
                        'status' => $data['status'],
                        'rejection_reason' => $data['status'] === 'rejected'
                            ? mb_substr(trim($data['reason']), 0, 255)
                            : null,
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                    ]);
            }
            return $locked->refresh();
        });

        if ($registration->status === 'approved') {
            try {
                Mail::raw(
                    "Your ShopEase courier registration has been approved. You can now sign in to the ShopEase mobile app using this email address. Set your password with Forgot Password and the one-time code sent to this email.",
                    fn ($mail) => $mail->to($registration->email)->subject('ShopEase Courier Account Approved')
                );
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'message' => $registration->status === 'approved'
                ? 'Courier registration approved and mobile account activated.'
                : 'Courier registration rejected.',
            'data' => $registration,
        ]);
    }
}
