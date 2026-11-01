<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function followers(Request $request): JsonResponse
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $rows = DB::table('buyer_shop_follows as follows')
            ->join('users', 'users.id', '=', 'follows.buyer_id')
            ->where('follows.seller_id', $seller->id)
            ->orderByDesc('follows.created_at')
            ->select(['users.id', 'users.name', 'users.profile_photo_path', 'follows.created_at as followed_at'])
            ->get()
            ->map(fn ($buyer): array => [
                'id' => (int) $buyer->id,
                'name' => $buyer->name,
                'avatar_url' => $buyer->profile_photo_path ? Storage::disk('public')->url($buyer->profile_photo_path) : null,
                'followed_at' => $buyer->followed_at,
            ])->values();

        return response()->json([
            'shop' => $seller->store_name ?: $seller->business_name ?: $request->user()->name,
            'total' => $rows->count(),
            'data' => $rows,
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $seller = Seller::query()->where('user_id', $user->id)->firstOrFail();

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'contact_no']),
            'seller' => $seller->only(['id', 'store_name', 'business_name', 'registration_status']),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_no' => ['required', 'string', 'max:30'],
        ]);

        $seller = Seller::query()->where('user_id', $user->id)->firstOrFail();
        $user->name = $validated['name'];
        $seller->contact_no = $validated['contact_no'];
        $user->save();
        $seller->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh()->only(['id', 'name', 'email', 'contact_no']),
        ]);
    }
}
