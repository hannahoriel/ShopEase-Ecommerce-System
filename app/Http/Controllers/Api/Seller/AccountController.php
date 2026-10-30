<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
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
