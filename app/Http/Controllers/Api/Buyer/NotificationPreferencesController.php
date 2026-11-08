<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Services\BuyerNotificationPreferences;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotificationPreferencesController extends Controller
{
    public function show(Request $request, BuyerNotificationPreferences $preferences): JsonResponse
    {
        return response()->json(['preferences' => $preferences->forUser($request->user())]);
    }

    public function update(Request $request, BuyerNotificationPreferences $preferences): JsonResponse
    {
        $data = $request->validate(['preferences' => ['required', 'array']]);
        $submitted = $data['preferences'];

        foreach (BuyerNotificationPreferences::DEFAULTS as $key => $_default) {
            if (! array_key_exists($key, $submitted) || ! is_bool($submitted[$key])) {
                throw ValidationException::withMessages([
                    'preferences' => ['Submit a true or false value for every notification setting.'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Notification preferences saved.',
            'preferences' => $preferences->save($request->user(), $submitted),
        ]);
    }
}
