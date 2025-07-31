<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return new UserResource($request->user()->load(['wallets', 'subscriptions', 'favorites']));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'current_password' => 'required_with:password',
            'password' => ['sometimes', 'confirmed', Password::defaults()],
            'preferred_language' => 'sometimes|exists:languages,code',
            'notification_settings' => 'sometimes|array',
            'notification_settings.email_airdrop_updates' => 'boolean',
            'notification_settings.email_weekly_digest' => 'boolean',
            'notification_settings.push_notifications' => 'boolean',
        ]);

        $user = $request->user();

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'The provided password does not match your current password.',
                ], 422);
            }
            $validated['password'] = Hash::make($validated['password']);
        }

        // Update notification settings
        if (isset($validated['notification_settings'])) {
            $user->notification_settings = array_merge(
                $user->notification_settings ?? [],
                $validated['notification_settings']
            );
            unset($validated['notification_settings']);
        }

        $user->update($validated);

        return new UserResource($user->fresh()->load(['wallets', 'subscriptions', 'favorites']));
    }
}