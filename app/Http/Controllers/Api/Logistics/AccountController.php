<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsAccountChangeRequest;
use App\Models\Logistics\LogisticsAccountSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    private const PROFILE_FIELDS = [
        'company_name', 'registered_business_name', 'company_type', 'sec_registration_number', 'tin',
        'company_email', 'company_phone', 'website', 'branch_count', 'years_in_operation',
        'representative_name', 'representative_position', 'representative_email', 'representative_phone',
        'region', 'province', 'city', 'barangay', 'postal_code', 'street', 'house_no', 'building', 'subdivision',
    ];

    private const NOTIFICATION_DEFAULTS = [
        'parcel_updates' => true, 'order_updates' => true, 'messages' => true,
        'delivery_exceptions' => true, 'compliance' => true, 'email_alerts' => true,
        'announcements' => true, 'promotions' => false,
    ];

    public function page(Request $request)
    {
        $data = $this->accountData($request->user());
        $cover = $data['cover_photo_path']
            ? Storage::disk('public')->url($data['cover_photo_path'])
            : null;
        $avatar = $request->user()->profile_photo_path
            ? Storage::disk('public')->url($request->user()->profile_photo_path)
            : null;

        return view('pages.logistics.account', [
            'accountProfile' => $data['profile'],
            'accountPreferences' => $data['preferences'],
            'accountChangeRequests' => $data['change_requests'],
            'accountCoverPhotoUrl' => $cover,
            'accountAvatarUrl' => $avatar,
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->accountData($request->user())]);
    }

    public function requestProfileChange(Request $request): JsonResponse
    {
        $rules = [];
        foreach (self::PROFILE_FIELDS as $field) {
            $rules[$field] = match ($field) {
                'company_email', 'representative_email' => ['sometimes', 'nullable', 'email', 'max:255'],
                'website' => ['sometimes', 'nullable', 'url', 'max:255'],
                'branch_count', 'years_in_operation' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100000'],
                default => ['sometimes', 'nullable', 'string', 'max:255'],
            };
        }
        $data = $request->validate($rules);
        $changes = collect($data)->only(self::PROFILE_FIELDS)->all();
        if ($changes === []) {
            throw ValidationException::withMessages(['profile' => 'Update at least one company profile field.']);
        }

        $pendingExists = LogisticsAccountChangeRequest::query()
            ->where('user_id', $request->user()->id)->where('section', 'profile')->where('status', 'pending')->exists();
        if ($pendingExists) {
            throw ValidationException::withMessages(['profile' => 'You already have a profile change request awaiting admin review.']);
        }

        $changeRequest = LogisticsAccountChangeRequest::query()->create([
            'user_id' => $request->user()->id,
            'section' => 'profile',
            'requested_data' => $changes,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Your profile change request was sent to ShopEase administration for review.',
            'data' => $this->serializeChangeRequest($changeRequest),
        ], 201);
    }

    public function savePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'privacy_settings' => ['sometimes', 'array'],
            'privacy_settings.public_company_contact' => ['sometimes', 'boolean'],
            'privacy_settings.share_delivery_updates' => ['sometimes', 'boolean'],
            'notification_preferences' => ['sometimes', 'array'],
            'notification_preferences.parcel_updates' => ['sometimes', 'boolean'],
            'notification_preferences.order_updates' => ['sometimes', 'boolean'],
            'notification_preferences.messages' => ['sometimes', 'boolean'],
            'notification_preferences.delivery_exceptions' => ['sometimes', 'boolean'],
            'notification_preferences.compliance' => ['sometimes', 'boolean'],
            'notification_preferences.email_alerts' => ['sometimes', 'boolean'],
            'notification_preferences.announcements' => ['sometimes', 'boolean'],
            'notification_preferences.promotions' => ['sometimes', 'boolean'],
        ]);
        if (! isset($data['privacy_settings']) && ! isset($data['notification_preferences'])) {
            throw ValidationException::withMessages(['preferences' => 'Provide privacy settings or notification preferences.']);
        }

        $settings = LogisticsAccountSetting::query()->firstOrNew(['user_id' => $request->user()->id]);
        if (isset($data['privacy_settings'])) {
            $settings->privacy_settings = array_merge($settings->privacy_settings ?? [
                'public_company_contact' => false, 'share_delivery_updates' => true,
            ], $data['privacy_settings']);
        }
        if (isset($data['notification_preferences'])) {
            $settings->notification_preferences = array_merge(
                self::NOTIFICATION_DEFAULTS,
                $settings->notification_preferences ?? [],
                $data['notification_preferences']
            );
        }
        $settings->save();

        return response()->json(['message' => 'Account preferences saved.', 'data' => [
            'privacy_settings' => $settings->privacy_settings,
            'notification_preferences' => $settings->notification_preferences,
        ]]);
    }

    public function uploadCoverPhoto(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cover_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $settings = LogisticsAccountSetting::query()->firstOrNew(['user_id' => $request->user()->id]);
        $oldPath = $settings->cover_photo_path;
        $path = $data['cover_photo']->store("logistics-account/{$request->user()->id}/cover", 'public');
        if (! $path) {
            return response()->json(['message' => 'Unable to store the cover photo.'], 500);
        }
        $settings->cover_photo_path = $path;
        $settings->save();
        if ($oldPath && str_starts_with($oldPath, "logistics-account/{$request->user()->id}/cover/")) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'message' => 'Cover photo saved.',
            'cover_photo_url' => Storage::disk('public')->url($path),
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === User::ROLE_ADMIN, 403);
        $filters = $request->validate(['status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])]]);
        $query = LogisticsAccountChangeRequest::query()->with('user:id,name,email')
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        return response()->json(['data' => $query->latest()->paginate(25)]);
    }

    public function review(Request $request, LogisticsAccountChangeRequest $changeRequest): JsonResponse
    {
        abort_unless($request->user()->role === User::ROLE_ADMIN, 403);
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $changeRequest = DB::transaction(function () use ($request, $changeRequest, $data): LogisticsAccountChangeRequest {
            $changeRequest = LogisticsAccountChangeRequest::query()->lockForUpdate()->findOrFail($changeRequest->id);
            abort_unless($changeRequest->status === 'pending', 409, 'This request has already been reviewed.');

            if ($data['status'] === 'approved' && $changeRequest->section === 'profile') {
                $logistics = Logistics::query()->where('user_id', $changeRequest->user_id)->lockForUpdate()->firstOrFail();
                $details = $logistics->registration_details ?? [];
                $details['company'] = array_merge($details['company'] ?? [], $changeRequest->requested_data ?? []);
                $logistics->registration_details = $details;
                $fieldMap = [
                    'company_name' => 'business_name', 'company_phone' => 'contact_no',
                    'province' => 'province', 'city' => 'municipality', 'barangay' => 'barangay',
                    'street' => 'street', 'house_no' => 'house_number',
                ];
                $profileChanges = [];
                foreach ($fieldMap as $input => $column) {
                    if (array_key_exists($input, $changeRequest->requested_data ?? [])) {
                        $profileChanges[$column] = $changeRequest->requested_data[$input];
                    }
                }
                $logistics->fill($profileChanges)->save();
            }

            $changeRequest->forceFill([
                'status' => $data['status'], 'review_note' => $data['review_note'] ?? null,
                'reviewed_by' => $request->user()->id, 'reviewed_at' => now(),
            ])->save();

            return $changeRequest->fresh('user:id,name,email');
        });

        return response()->json(['message' => 'Account change request reviewed.', 'data' => $this->serializeChangeRequest($changeRequest)]);
    }

    private function accountData(User $user): array
    {
        $logistics = Logistics::query()->where('user_id', $user->id)->firstOrFail();
        $details = $logistics->registration_details ?? [];
        $company = is_array($details['company'] ?? null) ? $details['company'] : [];
        $profile = array_merge([
            'company_name' => $logistics->business_name,
            'company_email' => $user->email,
            'company_phone' => $logistics->contact_no,
            'province' => $logistics->province,
            'city' => $logistics->municipality,
            'barangay' => $logistics->barangay,
            'street' => $logistics->street,
            'house_no' => $logistics->house_number,
        ], $company);
        $settings = LogisticsAccountSetting::query()->firstOrCreate(['user_id' => $user->id]);
        $settings->forceFill([
            'privacy_settings' => array_merge(['public_company_contact' => false, 'share_delivery_updates' => true], $settings->privacy_settings ?? []),
            'notification_preferences' => array_merge(self::NOTIFICATION_DEFAULTS, $settings->notification_preferences ?? []),
        ])->save();
        $requests = LogisticsAccountChangeRequest::query()->where('user_id', $user->id)
            ->latest()->limit(20)->get()->map(fn ($entry) => $this->serializeChangeRequest($entry))->all();

        return [
            'profile' => $profile,
            'preferences' => [
                'privacy_settings' => $settings->privacy_settings,
                'notification_preferences' => $settings->notification_preferences,
            ],
            'cover_photo_path' => $settings->cover_photo_path,
            'profile_photo_url' => $user->profile_photo_path ? Storage::disk('public')->url($user->profile_photo_path) : null,
            'change_requests' => $requests,
        ];
    }

    private function serializeChangeRequest(LogisticsAccountChangeRequest $request): array
    {
        return [
            'id' => $request->id,
            'section' => $request->section,
            'requested_data' => $request->requested_data,
            'status' => $request->status,
            'review_note' => $request->review_note,
            'created_at' => $request->created_at?->toISOString(),
            'reviewed_at' => $request->reviewed_at?->toISOString(),
            'user' => $request->relationLoaded('user') ? $request->user : null,
        ];
    }
}
