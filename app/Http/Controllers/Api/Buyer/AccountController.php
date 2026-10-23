<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Buyer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === User::ROLE_BUYER, 403);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:10'],
            'sex' => ['required', Rule::in(['Male', 'Female', 'Prefer not to say'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_no' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'province' => ['required', 'string', 'max:150'],
            'municipality' => ['required', 'string', 'max:150'],
            'barangay' => ['required', 'string', 'max:150'],
            'zip_code' => ['required', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:150'],
            'house_number' => ['nullable', 'string', 'max:100'],
            'building' => ['nullable', 'string', 'max:150'],
            'subdivision' => ['nullable', 'string', 'max:150'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $coverPhoto = $validated['cover_photo'] ?? null;
        $newCoverPath = $coverPhoto?->store("buyer-covers/{$user->id}", 'public');
        $buyer = $user->buyerProfile()->firstOrNew();
        $oldCoverPath = $buyer->cover_photo_path;
        $birthday = \Illuminate\Support\Carbon::parse($validated['birthday']);

        try {
            DB::transaction(function () use ($user, $buyer, $validated, $newCoverPath, $birthday): void {
                $buyer->fill([
                    'first_name' => trim($validated['first_name']),
                    'last_name' => trim($validated['last_name']),
                    'middle_initial' => $validated['middle_name'] ?? null,
                    'sex' => $validated['sex'],
                    'contact_no' => trim($validated['contact_no']),
                    'birthday' => $birthday->toDateString(),
                    'age' => $birthday->age,
                    'province' => trim($validated['province']),
                    'municipality' => trim($validated['municipality']),
                    'barangay' => trim($validated['barangay']),
                    'zip_code' => trim($validated['zip_code']),
                    'street' => $validated['street'] ?? null,
                    'house_number' => $validated['house_number'] ?? null,
                    'building' => $validated['building'] ?? null,
                    'subdivision' => $validated['subdivision'] ?? null,
                    'cover_photo_path' => $newCoverPath ?? $buyer->cover_photo_path,
                ]);
                $buyer->user()->associate($user);
                $buyer->save();

                $user->forceFill([
                    'name' => trim(implode(' ', array_filter([
                        $validated['first_name'],
                        $validated['middle_name'] ?? null,
                        $validated['last_name'],
                    ]))),
                    'email' => trim($validated['email']),
                ])->save();
            });
        } catch (\Throwable $exception) {
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            throw $exception;
        }

        if ($newCoverPath && $oldCoverPath && str_starts_with($oldCoverPath, "buyer-covers/{$user->id}/")) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        $buyer->refresh();
        $user->refresh();

        return response()->json([
            'message' => 'Buyer account updated successfully.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_path
                    ? Storage::disk('public')->url($user->profile_photo_path)
                    : null,
            ],
            'profile' => [
                'first_name' => $buyer->first_name,
                'last_name' => $buyer->last_name,
                'middle_name' => $buyer->middle_initial,
                'sex' => $buyer->sex,
                'contact_no' => $buyer->contact_no,
                'birthday' => $buyer->birthday?->toDateString(),
                'age' => $buyer->age,
                'province' => $buyer->province,
                'municipality' => $buyer->municipality,
                'barangay' => $buyer->barangay,
                'zip_code' => $buyer->zip_code,
                'street' => $buyer->street,
                'house_number' => $buyer->house_number,
                'building' => $buyer->building,
                'subdivision' => $buyer->subdivision,
                'cover_photo_url' => $buyer->cover_photo_path
                    ? Storage::disk('public')->url($buyer->cover_photo_path)
                    : null,
            ],
        ]);
    }
}
