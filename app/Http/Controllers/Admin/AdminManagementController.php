<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminManagementController extends Controller
{
    private const MODULES = [
        'Dashboard',
        'Registrations',
        'User Management',
        'Seller Compliance',
        'Complaints and Disputes',
        'Commission',
        'Logistics Management',
        'Reports',
        'Platform Settings',
        'Notifications',
        'Account Management',
        'Messages',
    ];

    private const MESSAGE_CATEGORIES = ['Logistics', 'Complaints', 'Buyer', 'Seller'];

    public function index(Request $request): JsonResponse
    {
        $this->requireManager($request);

        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get()
            ->map(function (User $user): array {
                $profile = DB::table('admin_account_profiles')
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'permissions' => $this->decodeArray($profile->permissions ?? null),
                    'message_permissions' => $this->decodeArray($profile->message_permissions ?? null),
                    'is_enabled' => (bool) ($profile->is_enabled ?? true),
                    'created_at' => $user->created_at?->toISOString(),
                ];
            });

        return response()->json(['data' => $admins]);
    }

    public function idPhoto(Request $request, User $user)
    {
        $actor = $request->user();
        abort_unless($actor?->role === User::ROLE_ADMIN, 403);

        $profile = DB::table('admin_account_profiles')
            ->where('user_id', $user->id)
            ->first(['admin_id_photo_path']);
        abort_unless($profile?->admin_id_photo_path, 404);

        if (! $actor->is($user)) {
            $this->requireManager($request);
        }

        $disk = Storage::disk('local');
        abort_unless($disk->exists($profile->admin_id_photo_path), 404);

        return $disk->response($profile->admin_id_photo_path);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireManager($request);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'contact_number' => ['nullable', 'regex:/^(9\d{9}|09\d{9})$/'],
            'birthday' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', Rule::in(['Male', 'Female', 'Prefer not to say'])],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'admin_id_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', Rule::in(self::MODULES)],
            'message_permissions' => ['nullable', 'array'],
            'message_permissions.*' => ['required', 'string', Rule::in(self::MESSAGE_CATEGORIES)],
            'password' => ['required', 'string', 'min:12', 'max:128'],
        ]);

        $permissions = array_values(array_unique($data['permissions']));
        $messagePermissions = array_values(array_unique($data['message_permissions'] ?? []));

        if (in_array('Messages', $permissions, true) && $messagePermissions === []) {
            throw ValidationException::withMessages([
                'message_permissions' => 'Choose at least one Messages category.',
            ]);
        }

        if (! in_array('Messages', $permissions, true) && $messagePermissions !== []) {
            throw ValidationException::withMessages([
                'message_permissions' => 'Messages categories require Messages module access.',
            ]);
        }

        $storedFiles = [];

        try {
            foreach (['profile_photo', 'admin_id_photo'] as $field) {
                if ($request->hasFile($field)) {
                    $disk = $field === 'admin_id_photo' ? 'local' : 'public';
                    $storedFiles[$field] = $request->file($field)->store('admin-accounts', $disk);
                }
            }

            $user = DB::transaction(function () use ($data, $permissions, $messagePermissions, $storedFiles): User {
                $name = implode(' ', array_filter([
                    trim($data['first_name']),
                    isset($data['middle_name']) ? trim($data['middle_name']) : null,
                    trim($data['last_name']),
                ]));

                $user = User::create([
                    'name' => $name,
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => User::ROLE_ADMIN,
                    'email_verified_at' => now(),
                    'registration_status' => 'active',
                ]);

                DB::table('admin_account_profiles')->insert([
                    'user_id' => $user->id,
                    'first_name' => trim($data['first_name']),
                    'middle_name' => isset($data['middle_name']) ? trim($data['middle_name']) : null,
                    'last_name' => trim($data['last_name']),
                    'birthday' => $data['birthday'] ?? null,
                    'sex' => $data['sex'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'profile_photo_path' => $storedFiles['profile_photo'] ?? null,
                    'admin_id_photo_path' => $storedFiles['admin_id_photo'] ?? null,
                    'permissions' => json_encode($permissions, JSON_THROW_ON_ERROR),
                    'message_permissions' => json_encode($messagePermissions, JSON_THROW_ON_ERROR),
                    'is_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return $user;
            });
        } catch (\Throwable $exception) {
            foreach ($storedFiles as $field => $path) {
                Storage::disk($field === 'admin_id_photo' ? 'local' : 'public')->delete($path);
            }
            throw $exception;
        }

        return response()->json([
            'message' => 'Administrator account created successfully.',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $permissions,
                'message_permissions' => $messagePermissions,
                'is_enabled' => true,
            ],
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->requireManager($request);
        abort_unless($user->role === User::ROLE_ADMIN, 404);
        abort_if($user->is($request->user()), 422, 'Use your own account settings to change this account.');

        $data = $request->validate([
            'permissions' => ['sometimes', 'required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', Rule::in(self::MODULES)],
            'message_permissions' => ['sometimes', 'array'],
            'message_permissions.*' => ['required', 'string', Rule::in(self::MESSAGE_CATEGORIES)],
            'is_enabled' => ['sometimes', 'boolean'],
            'password' => ['sometimes', 'required', 'string', 'min:12', 'max:128'],
        ]);

        $profile = DB::table('admin_account_profiles')->where('user_id', $user->id)->first();
        $permissions = array_values(array_unique(
            $data['permissions'] ?? $this->decodeArray($profile->permissions ?? null)
        ));
        $messagePermissions = array_values(array_unique(
            $data['message_permissions'] ?? $this->decodeArray($profile->message_permissions ?? null)
        ));

        if (
            in_array('Messages', $permissions, true)
            && $messagePermissions === []
            && (array_key_exists('permissions', $data) || array_key_exists('message_permissions', $data))
        ) {
            throw ValidationException::withMessages([
                'message_permissions' => 'Choose at least one Messages category.',
            ]);
        }

        if (
            ! in_array('Messages', $permissions, true)
            && $messagePermissions !== []
            && (array_key_exists('permissions', $data) || array_key_exists('message_permissions', $data))
        ) {
            throw ValidationException::withMessages([
                'message_permissions' => 'Messages categories require Messages module access.',
            ]);
        }

        DB::transaction(function () use ($user, $data, $permissions, $messagePermissions, $profile): void {
            $userChanges = [];
            if (array_key_exists('password', $data)) {
                $userChanges['password'] = $data['password'];
            }
            if ($userChanges !== []) {
                $user->update($userChanges);
            }

            $profileChanges = ['updated_at' => now()];
            if (array_key_exists('permissions', $data)) {
                $profileChanges['permissions'] = json_encode($permissions, JSON_THROW_ON_ERROR);
            }
            if (array_key_exists('message_permissions', $data)) {
                $profileChanges['message_permissions'] = json_encode($messagePermissions, JSON_THROW_ON_ERROR);
            }
            if (array_key_exists('is_enabled', $data)) {
                $profileChanges['is_enabled'] = $data['is_enabled'];
            }

            if ($profile) {
                DB::table('admin_account_profiles')->where('user_id', $user->id)->update($profileChanges);
            } else {
                DB::table('admin_account_profiles')->insert([
                    'user_id' => $user->id,
                    'created_at' => now(),
                ] + $profileChanges);
            }
        });

        return response()->json(['message' => 'Administrator account updated successfully.']);
    }

    private function requireManager(Request $request): void
    {
        $actor = $request->user();
        abort_unless($actor?->role === User::ROLE_ADMIN, 403);

        $profile = DB::table('admin_account_profiles')
            ->where('user_id', $actor->id)
            ->first(['permissions', 'is_enabled']);

        abort_if($profile && ! $profile->is_enabled, 403, 'This administrator account is disabled.');
        abort_unless($profile?->permissions === null, 403, 'Only a full administrator can manage administrator accounts.');
    }

    private function decodeArray(?string $value): array
    {
        $decoded = $value === null ? null : json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
