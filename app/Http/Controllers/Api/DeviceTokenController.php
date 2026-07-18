<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * Register (or refresh) an FCM device token for the authenticated user.
     * Mobile apps call this after login and on token refresh.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:512',
            'platform' => 'nullable|in:android,ios,web',
            'device_name' => 'nullable|string|max:255',
        ]);

        // A token identifies one physical device; reassign it if another user owned it
        DeviceToken::where('token', $validated['token'])
            ->where('user_id', '!=', $request->user()->id)
            ->delete();

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'token' => $validated['token'],
            ],
            [
                'platform' => $validated['platform'] ?? 'android',
                'device_name' => $validated['device_name'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Device registered for push notifications',
            'device_token' => $deviceToken,
        ], 201);
    }

    /**
     * Remove a device token (e.g. on logout).
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:512',
        ]);

        DeviceToken::where('user_id', $request->user()->id)
            ->where('token', $validated['token'])
            ->delete();

        return response()->json(['message' => 'Device token removed']);
    }
}
