<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $newPath = $validated['profile_photo']->store("profile-photos/{$user->id}", 'public');
        $oldPath = $user->profile_photo_path;

        $user->profile_photo_path = $newPath;
        $user->save();

        if ($oldPath && str_starts_with($oldPath, "profile-photos/{$user->id}/")) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'message' => 'Profile photo updated successfully.',
            'profile_photo_url' => Storage::disk('public')->url($newPath),
        ]);
    }
}
