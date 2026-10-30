<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\Rider\Rider;
use App\Models\User;
use App\Mail\SortingCenterCredentials;
use App\Mail\DeliveryHubCredentials;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class NetworkHubController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $centers = LogisticsBranch::query()
            ->where('logistics_id', $logistics->id)
            ->whereNull('archived_at')
            ->with(['deliveryHubs' => fn ($query) => $query->whereNull('archived_at')])
            ->orderBy('name')
            ->get()
            ->map(fn (LogisticsBranch $center): array => $this->serializeCenter($center));

        return response()->json(['data' => $centers]);
    }

    public function archive(Request $request): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $centers = LogisticsBranch::query()
            ->where('logistics_id', $logistics->id)
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get()
            ->map(fn (LogisticsBranch $center): array => $this->serializeCenter($center));

        return response()->json(['data' => $centers]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateCenter($request);
        $center = DB::transaction(function () use ($request, $validated): LogisticsBranch {
            $logistics = $this->lockLogistics($request->user());
            $this->ensureCenterIsUnique($logistics, $validated);
            $password = $this->generateLoginPassword();
            $branchUser = User::query()->create([
                'name' => $validated['branch_name'],
                'email' => mb_strtolower($validated['login_email']),
                'password' => $password,
                'role' => User::ROLE_SORTING_CENTER,
                'email_verified_at' => now(),
                'registration_status' => 'active',
            ]);

            $center = $logistics->branches()->create([
                ...$this->centerAttributes($validated),
                'user_id' => $branchUser->id,
            ]);

            Mail::to($branchUser->email)->send(new SortingCenterCredentials(
                branchName: $center->name,
                email: $branchUser->email,
                password: $password,
            ));

            return $center;
        });

        return response()->json([
            'message' => 'Sorting center created successfully.',
            'data' => $this->serializeCenter($center->load('deliveryHubs')),
        ], 201);
    }

    public function update(Request $request, int $center): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branch = $this->ownedCenter($logistics, $center, archived: false);
        $validated = $this->validateCenter($request);
        DB::transaction(function () use ($request, $center, $validated): void {
            $logistics = $this->lockLogistics($request->user());
            $branch = $this->ownedCenter($logistics, $center, archived: false);
            $this->ensureCenterIsUnique($logistics, $validated, $branch);
            $branch->update($this->centerAttributes($validated));
            if ($branch->user_id) {
                $branch->user()->update([
                    'name' => $validated['branch_name'],
                    'email' => mb_strtolower($validated['login_email']),
                ]);
            }
        });
        $branch = $this->ownedCenter($logistics, $center, archived: false);

        return response()->json([
            'message' => 'Sorting center updated successfully.',
            'data' => $this->serializeCenter($branch->fresh([
                'deliveryHubs' => fn ($query) => $query->whereNull('archived_at'),
            ])),
        ]);
    }

    public function archiveCenter(Request $request, int $center): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branch = $this->ownedCenter($logistics, $center, archived: false);
        $branch->forceFill(['archived_at' => now()])->save();

        return response()->json(['message' => 'Sorting center moved to archive.']);
    }

    public function restoreCenter(Request $request, int $center): JsonResponse
    {
        $branch = DB::transaction(function () use ($request, $center): LogisticsBranch {
            $logistics = $this->lockLogistics($request->user());
            $branch = $this->ownedCenter($logistics, $center, archived: true);
            $provinceInUse = LogisticsBranch::query()
                ->where('logistics_id', $logistics->id)
                ->whereNull('archived_at')
                ->whereRaw('LOWER(province) = ?', [mb_strtolower((string) $branch->province)])
                ->exists();
            if ($provinceInUse) {
                throw ValidationException::withMessages([
                    'province' => 'Another active sorting center already exists in this province.',
                ]);
            }

            $this->ensureLoginEmailAvailable($logistics, (string) $branch->login_email, centerId: $branch->id);
            $branch->forceFill(['archived_at' => null])->save();

            return $branch;
        });

        return response()->json([
            'message' => 'Sorting center restored successfully.',
            'data' => $this->serializeCenter($branch->fresh([
                'deliveryHubs' => fn ($query) => $query->whereNull('archived_at'),
            ])),
        ]);
    }

    public function hubs(Request $request, int $center): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branch = $this->ownedCenter($logistics, $center, archived: false);
        $hubs = $branch->deliveryHubs()
            ->whereNull('archived_at')
            ->orderBy('hub_name')
            ->get()
            ->map(fn (LogisticsDeliveryHub $hub): array => $this->serializeHub($hub));

        return response()->json(['data' => $hubs]);
    }

    public function hubArchive(Request $request, int $center): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branch = $this->ownedCenter($logistics, $center, archived: false);
        $hubs = $branch->deliveryHubs()
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get()
            ->map(fn (LogisticsDeliveryHub $hub): array => $this->serializeHub($hub));

        return response()->json([
            'data' => $hubs->map(fn (array $hub): array => [...$hub, 'center_id' => $branch->id]),
        ]);
    }

    public function storeHub(Request $request, int $center): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $branch = $this->ownedCenter($logistics, $center, archived: false);
        $validated = $this->validateHub($request, $branch);
        $hub = DB::transaction(function () use ($request, $center, $validated): LogisticsDeliveryHub {
            $logistics = $this->lockLogistics($request->user());
            $branch = $this->ownedCenter($logistics, $center, archived: false);
            $this->ensureHubIsUnique($logistics, $branch, $validated);

            $password = $this->generateLoginPassword();
            $hubUser = User::query()->create([
                'name' => $validated['hub_name'],
                'email' => mb_strtolower($validated['login_email']),
                'password' => $password,
                'role' => User::ROLE_DELIVERY_HUB,
                'email_verified_at' => now(),
                'registration_status' => 'active',
            ]);

            $hub = $branch->deliveryHubs()->create([
                ...$this->hubAttributes($validated, $branch),
                'user_id' => $hubUser->id,
            ]);

            Mail::to($hubUser->email)->send(new DeliveryHubCredentials(
                hubName: $hub->hub_name,
                email: $hubUser->email,
                password: $password,
            ));

            return $hub;
        });

        return response()->json([
            'message' => 'Delivery hub created successfully.',
            'data' => $this->serializeHub($hub),
        ], 201);
    }

    public function updateHub(Request $request, int $hub): JsonResponse
    {
        $logistics = $this->logisticsFor($request->user());
        $deliveryHub = $this->ownedHub($logistics, $hub, archived: false);
        $branch = $deliveryHub->logisticsBranch;
        $validated = $this->validateHub($request, $branch);
        DB::transaction(function () use ($request, $hub, $validated): void {
            $logistics = $this->lockLogistics($request->user());
            $deliveryHub = $this->ownedHub($logistics, $hub, archived: false);
            $branch = $deliveryHub->logisticsBranch;
            $this->ensureHubIsUnique($logistics, $branch, $validated, $deliveryHub);
            $deliveryHub->update($this->hubAttributes($validated, $branch));
            if ($deliveryHub->user_id) {
                $deliveryHub->user()->update([
                    'name' => $validated['hub_name'],
                    'email' => mb_strtolower($validated['login_email']),
                ]);
            }
        });
        $deliveryHub = $this->ownedHub($logistics, $hub, archived: false);

        return response()->json([
            'message' => 'Delivery hub updated successfully.',
            'data' => $this->serializeHub($deliveryHub->fresh()),
        ]);
    }

    public function archiveHub(Request $request, int $hub): JsonResponse
    {
        $deliveryHub = $this->ownedHub($this->logisticsFor($request->user()), $hub, archived: false);
        $deliveryHub->forceFill(['archived_at' => now()])->save();

        return response()->json(['message' => 'Delivery hub moved to archive.']);
    }

    public function restoreHub(Request $request, int $hub): JsonResponse
    {
        $deliveryHub = DB::transaction(function () use ($request, $hub): LogisticsDeliveryHub {
            $logistics = $this->lockLogistics($request->user());
            $deliveryHub = $this->ownedHub($logistics, $hub, archived: true);
            $branch = $deliveryHub->logisticsBranch;

            $duplicateName = $branch->deliveryHubs()
                ->whereNull('archived_at')
                ->whereRaw('LOWER(hub_name) = ?', [mb_strtolower($deliveryHub->hub_name)])
                ->exists();
            if ($duplicateName) {
                throw ValidationException::withMessages([
                    'hub_name' => 'A delivery hub with this name already exists in this sorting center.',
                ]);
            }

            $this->ensureLoginEmailAvailable($logistics, $deliveryHub->login_email, hubId: $deliveryHub->id);
            $deliveryHub->forceFill(['archived_at' => null])->save();

            return $deliveryHub;
        });

        return response()->json([
            'message' => 'Delivery hub restored successfully.',
            'data' => $this->serializeHub($deliveryHub->fresh()),
        ]);
    }

    private function validateCenter(Request $request): array
    {
        $locationRule = ['required', 'string', 'max:120'];

        return $request->validate([
            'branch_name' => ['required', 'string', 'max:120'],
            'province' => $locationRule,
            'city' => $locationRule,
            'barangay' => $locationRule,
            'postal_code' => ['required', 'regex:/^[0-9]{4}$/'],
            'house_no' => ['nullable', 'string', 'max:80'],
            'street' => ['required', 'string', 'max:150'],
            'building' => ['nullable', 'string', 'max:150'],
            'subdivision' => ['nullable', 'string', 'max:150'],
            'manager_name' => ['required', 'string', 'max:120'],
            'manager_email' => ['required', 'email', 'max:255'],
            'manager_phone' => ['required', 'regex:/^(09[0-9]{9}|\+639[0-9]{9})$/'],
            'login_email' => ['required', 'email', 'max:255'],
        ]);
    }

    private function validateHub(Request $request, LogisticsBranch $branch): array
    {
        $validated = $request->validate([
            'hub_name' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'barangay' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'regex:/^[0-9]{4}$/'],
            'house_no' => ['nullable', 'string', 'max:80'],
            'street' => ['required', 'string', 'max:150'],
            'building' => ['nullable', 'string', 'max:150'],
            'subdivision' => ['nullable', 'string', 'max:150'],
            'manager_name' => ['required', 'string', 'max:120'],
            'manager_email' => ['required', 'email', 'max:255'],
            'manager_phone' => ['required', 'regex:/^(09[0-9]{9}|\+639[0-9]{9})$/'],
            'login_email' => ['required', 'email', 'max:255'],
        ]);

        $validated['province'] = $branch->province;

        return $validated;
    }

    private function centerAttributes(array $validated): array
    {
        return [
            'name' => $validated['branch_name'],
            'address' => mb_substr(implode(', ', array_filter([
                $validated['house_no'] ?? null,
                $validated['street'],
                $validated['building'] ?? null,
                $validated['subdivision'] ?? null,
                $validated['barangay'],
                $validated['city'],
                $validated['province'],
                $validated['postal_code'],
            ])), 0, 500),
            'contact_person' => $validated['manager_name'],
            'phone' => $validated['manager_phone'],
            'province' => $validated['province'],
            'municipality' => $validated['city'],
            'barangay' => $validated['barangay'],
            'postal_code' => $validated['postal_code'],
            'house_no' => $validated['house_no'] ?? null,
            'street' => $validated['street'],
            'building' => $validated['building'] ?? null,
            'subdivision' => $validated['subdivision'] ?? null,
            'manager_email' => $validated['manager_email'],
            'login_email' => mb_strtolower($validated['login_email']),
            'status' => 'active',
        ];
    }

    private function hubAttributes(array $validated, LogisticsBranch $branch): array
    {
        return [
            'logistics_branch_id' => $branch->id,
            'hub_name' => $validated['hub_name'],
            'province' => $branch->province,
            'municipality' => $validated['city'],
            'barangay' => $validated['barangay'],
            'postal_code' => $validated['postal_code'],
            'house_no' => $validated['house_no'] ?? null,
            'street' => $validated['street'],
            'building' => $validated['building'] ?? null,
            'subdivision' => $validated['subdivision'] ?? null,
            'manager_name' => $validated['manager_name'],
            'manager_email' => $validated['manager_email'],
            'manager_phone' => $validated['manager_phone'],
            'login_email' => mb_strtolower($validated['login_email']),
            'status' => 'active',
        ];
    }

    private function ensureCenterIsUnique(
        Logistics $logistics,
        array $validated,
        ?LogisticsBranch $except = null
    ): void {
        $provinceInUse = LogisticsBranch::query()
            ->where('logistics_id', $logistics->id)
            ->whereNull('archived_at')
            ->when($except, fn (Builder $query) => $query->where('id', '<>', $except->id))
            ->whereRaw('LOWER(province) = ?', [mb_strtolower($validated['province'])])
            ->exists();
        if ($provinceInUse) {
            throw ValidationException::withMessages([
                'province' => 'A sorting center already exists for this province.',
            ]);
        }

        $this->ensureLoginEmailAvailable($logistics, $validated['login_email'], centerId: $except?->id);
        $userExists = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['login_email'])])
            ->when($except?->user_id, fn (Builder $query, int $userId) => $query->where('id', '<>', $userId))
            ->exists();
        if ($userExists) {
            throw ValidationException::withMessages([
                'login_email' => 'This email is already linked to an account.',
            ]);
        }
    }

    private function ensureHubIsUnique(
        Logistics $logistics,
        LogisticsBranch $branch,
        array $validated,
        ?LogisticsDeliveryHub $except = null
    ): void {
        $nameInUse = $branch->deliveryHubs()
            ->whereNull('archived_at')
            ->when($except, fn (Builder $query) => $query->where('id', '<>', $except->id))
            ->whereRaw('LOWER(hub_name) = ?', [mb_strtolower($validated['hub_name'])])
            ->exists();
        if ($nameInUse) {
            throw ValidationException::withMessages([
                'hub_name' => 'A delivery hub with this name already exists in this sorting center.',
            ]);
        }

        $this->ensureLoginEmailAvailable($logistics, $validated['login_email'], hubId: $except?->id);
        $userExists = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['login_email'])])
            ->when($except?->user_id, fn (Builder $query, int $userId) => $query->where('id', '<>', $userId))
            ->exists();
        if ($userExists) {
            throw ValidationException::withMessages([
                'login_email' => 'This email is already linked to an account.',
            ]);
        }
    }

    private function ensureLoginEmailAvailable(
        Logistics $logistics,
        string $email,
        ?int $centerId = null,
        ?int $hubId = null
    ): void {
        $normalizedEmail = mb_strtolower($email);
        $centerExists = LogisticsBranch::query()
            ->where('logistics_id', $logistics->id)
            ->whereNull('archived_at')
            ->when($centerId, fn (Builder $query) => $query->where('id', '<>', $centerId))
            ->whereRaw('LOWER(login_email) = ?', [$normalizedEmail])
            ->exists();
        $hubExists = LogisticsDeliveryHub::query()
            ->whereHas('logisticsBranch', fn (Builder $query) => $query
                ->where('logistics_id', $logistics->id)
                ->whereNull('archived_at'))
            ->whereNull('archived_at')
            ->when($hubId, fn (Builder $query) => $query->where('id', '<>', $hubId))
            ->whereRaw('LOWER(login_email) = ?', [$normalizedEmail])
            ->exists();

        if ($centerExists || $hubExists) {
            throw ValidationException::withMessages([
                'login_email' => 'This email is already used by a sorting center or delivery hub.',
            ]);
        }
    }

    private function serializeCenter(LogisticsBranch $center): array
    {
        $riders = $this->activeRiders($center)->get();
        $hubs = $center->relationLoaded('deliveryHubs')
            ? $center->deliveryHubs
            : $center->deliveryHubs()->whereNull('archived_at')->get();

        return [
            'id' => $center->id,
            'branch_name' => $center->name,
            'province' => $center->province,
            'city' => $center->municipality,
            'barangay' => $center->barangay,
            'postal_code' => $center->postal_code,
            'house_no' => $center->house_no,
            'street' => $center->street,
            'building' => $center->building,
            'subdivision' => $center->subdivision,
            'manager_name' => $center->contact_person,
            'manager_email' => $center->manager_email,
            'manager_phone' => $center->phone,
            'login_email' => $center->login_email,
            'status' => $center->status ?? 'active',
            'archived_at' => $center->archived_at?->toISOString(),
            'delivery_hubs_count' => $hubs->count(),
            'archived_hubs_count' => $center->deliveryHubs()->whereNotNull('archived_at')->count(),
            'drivers_count' => $riders->count(),
            'vehicles_count' => $riders->filter(fn (Rider $rider): bool => filled($rider->vehicle))->count(),
            'vehicles' => $riders->filter(fn (Rider $rider): bool => filled($rider->vehicle))
                ->map(fn (Rider $rider): array => [
                    'id' => $rider->id,
                    'name' => $rider->vehicle,
                    'plate_number' => $rider->plate_number,
                    'type' => $rider->vehicle,
                    'model' => null,
                    'status' => 'active',
                ])->values(),
            'hubs' => $hubs->map(fn (LogisticsDeliveryHub $hub): array => $this->serializeHub($hub))->values(),
        ];
    }

    private function serializeHub(LogisticsDeliveryHub $hub): array
    {
        $riders = $this->activeRiders($hub->logisticsBranch)
            ->whereIn('municipal_hub', [$hub->hub_name, $hub->municipality])
            ->get();

        return [
            'id' => $hub->id,
            'hub_name' => $hub->hub_name,
            'province' => $hub->province,
            'city' => $hub->municipality,
            'barangay' => $hub->barangay,
            'postal_code' => $hub->postal_code,
            'house_no' => $hub->house_no,
            'street' => $hub->street,
            'building' => $hub->building,
            'subdivision' => $hub->subdivision,
            'manager_name' => $hub->manager_name,
            'manager_email' => $hub->manager_email,
            'manager_phone' => $hub->manager_phone,
            'login_email' => $hub->login_email,
            'status' => $hub->status,
            'archived_at' => $hub->archived_at?->toISOString(),
            'riders_count' => $riders->count(),
            'couriers_count' => $riders->filter(fn (Rider $rider): bool => filled($rider->courier_company))->count(),
            'vehicles_count' => $riders->filter(fn (Rider $rider): bool => filled($rider->vehicle))->count(),
        ];
    }

    private function activeRiders(LogisticsBranch $center): Builder
    {
        return Rider::query()
            ->where('logistics_branch_id', $center->id)
            ->where('registration_status', 'active')
            ->whereHas('user', fn (Builder $query) => $query->where('registration_status', 'active'));
    }

    private function ownedCenter(Logistics $logistics, int $centerId, bool $archived): LogisticsBranch
    {
        $query = LogisticsBranch::query()->where('logistics_id', $logistics->id);
        $archived ? $query->whereNotNull('archived_at') : $query->whereNull('archived_at');

        return $query->findOrFail($centerId);
    }

    private function ownedHub(Logistics $logistics, int $hubId, bool $archived): LogisticsDeliveryHub
    {
        $query = LogisticsDeliveryHub::query()
            ->whereHas('logisticsBranch', fn (Builder $query) => $query
                ->where('logistics_id', $logistics->id)
                ->whereNull('archived_at'))
            ->with('logisticsBranch');
        $archived ? $query->whereNotNull('archived_at') : $query->whereNull('archived_at');

        return $query->findOrFail($hubId);
    }

    private function logisticsFor(User $user): Logistics
    {
        return Logistics::query()
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
    }

    private function lockLogistics(User $user): Logistics
    {
        return Logistics::query()
            ->where('user_id', $user->id)
            ->where('registration_status', 'active')
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function generateLoginPassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%&*?';
        $allCharacters = $lowercase.$uppercase.$numbers.$special;

        $characters = [
            $lowercase[random_int(0, strlen($lowercase) - 1)],
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        ];

        for ($i = count($characters); $i < 8; $i++) {
            $characters[] = $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        for ($i = count($characters) - 1; $i > 0; $i--) {
            $swapIndex = random_int(0, $i);
            [$characters[$i], $characters[$swapIndex]] = [$characters[$swapIndex], $characters[$i]];
        }

        return implode('', $characters);
    }
}
