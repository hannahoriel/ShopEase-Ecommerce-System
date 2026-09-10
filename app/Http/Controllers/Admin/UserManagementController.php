<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountStatusChanged;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    private const SUSPEND_REASONS = [
        'Violation of platform policies',
        'Inappropriate behavior',
        'Listing of prohibited products',
        'Fraudulent activity',
        'Multiple complaints from users',
        'Other (please specify)',
    ];

    private const DEACTIVATE_REASONS = [
        'Severe violation of platform policies',
        'Fraudulent activity',
        'Abuse or harassment',
        'Request by the user',
        'Other (please specify)',
    ];

    public function index()
    {
        $this->authorizeAdmin(request());

        return view('pages.admin.user-management', [
            'users' => $this->userQuery()->get()->map(fn (User $user) => $this->serializeUser($user)),
            'counts' => $this->counts(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $query = $this->userQuery();
        $this->applyFilters($query, $request);

        $users = $query->orderByDesc('created_at')
            ->paginate(min($request->integer('per_page', 10), 100));

        return response()->json([
            'data' => $users->getCollection()->map(fn (User $user) => $this->serializeUser($user))->values(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
            'counts' => $this->counts(),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorizeAdmin(request());

        abort_unless(in_array($user->role, [User::ROLE_BUYER, User::ROLE_SELLER], true), 404);

        return response()->json($this->serializeUser($user, true));
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);

        abort_unless(in_array($user->role, [User::ROLE_BUYER, User::ROLE_SELLER], true), 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended', 'deactivated'])],
            'reason' => [
                Rule::requiredIf(fn () => in_array($request->string('status')->value(), ['suspended', 'deactivated'], true)),
                'nullable',
                'string',
                'max:100',
            ],
            'details' => ['nullable', 'string', 'max:300'],
            'duration' => [
                Rule::requiredIf(fn () => $request->string('status')->value() === 'suspended'),
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $status = $validated['status'];
        if ($status === 'suspended' && ! in_array($validated['reason'], self::SUSPEND_REASONS, true)) {
            return response()->json(['message' => 'The selected suspension reason is invalid.'], 422);
        }
        if ($status === 'deactivated' && ! in_array($validated['reason'], self::DEACTIVATE_REASONS, true)) {
            return response()->json(['message' => 'The selected deactivation reason is invalid.'], 422);
        }

        DB::transaction(function () use ($user, $status, $validated) {
            $user->forceFill([
                'registration_status' => $status,
                'suspended_until' => $status === 'suspended' ? now()->addDays((int) $validated['duration']) : null,
                'account_action_reason' => $validated['reason'] ?? null,
                'account_action_details' => $validated['details'] ?? null,
            ])->save();
        });

        $updatedUser = $user->fresh();

        if (in_array($status, ['active', 'suspended', 'deactivated'], true)) {
            Mail::to($updatedUser->email)->send(new AccountStatusChanged(
                user: $updatedUser,
                status: $status,
                reason: $validated['reason'] ?? null,
                details: $validated['details'] ?? null,
                duration: $status === 'suspended' ? (int) $validated['duration'] : null,
            ));
        }

        return response()->json([
            'message' => "{$updatedUser->name}'s account has been {$status}.",
            'user' => $this->serializeUser($updatedUser, true),
            'counts' => $this->counts(),
        ]);
    }

    private function userQuery()
    {
        return User::query()
            ->whereIn('role', [User::ROLE_BUYER, User::ROLE_SELLER])
            ->whereIn('registration_status', ['active', 'suspended', 'deactivated']);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === User::ROLE_ADMIN, 403);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%");
            });
        }

        if (in_array($type = $request->string('type')->value(), ['buyer', 'seller'], true)) {
            $query->where('role', $type);
        }

        if (in_array($status = $request->string('status')->value(), ['active', 'suspended', 'deactivated'], true)) {
            $query->where('registration_status', $status);
        }

        if ($date = $request->string('date')->value()) {
            $query->whereDate('created_at', $date);
        }
    }

    private function counts(): array
    {
        $query = $this->userQuery();

        return [
            'buyers' => (clone $query)->where('role', User::ROLE_BUYER)->count(),
            'sellers' => (clone $query)->where('role', User::ROLE_SELLER)->count(),
            'suspended' => (clone $query)->where('registration_status', 'suspended')->count(),
            'total' => $query->count(),
        ];
    }

    private function serializeUser(User $user, bool $details = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'type' => $user->role,
            'email' => $user->email,
            'phone' => $user->contact_no,
            'date' => $user->created_at?->toDateString(),
            'dateLabel' => $user->created_at?->format('F j, Y'),
            'timeLabel' => $user->created_at?->format('g:i A'),
            'status' => $user->registration_status ?: 'active',
            'suspensionDays' => $user->suspended_until?->isFuture()
                ? (int) round(now()->diffInDays($user->suspended_until))
                : 0,
        ];

        if ($details) {
            $data['details'] = array_merge($user->only([
                'first_name', 'last_name', 'middle_initial', 'sex', 'birthday', 'age', 'province',
                'municipality', 'barangay', 'street', 'house_number', 'zip_code', 'business_name',
                'line_of_business', 'upload_id', 'upload_business_permit', 'account_action_reason',
                'account_action_details',
            ]), [
                'valid_id_url' => $user->upload_id
                    ? Storage::disk('public')->url($user->upload_id)
                    : null,
                'business_permit_url' => $user->upload_business_permit
                    ? Storage::disk('public')->url($user->upload_business_permit)
                    : null,
            ]);
        }

        return $data;
    }
}
