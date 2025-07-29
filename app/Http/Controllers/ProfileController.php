<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\UserWallet;
use App\Models\Blockchain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user()->load(['wallets.blockchain', 'subscriptions.airdrop', 'favorites.airdrop']);
        $blockchains = Blockchain::active()->get();

        return view('profile.index', compact('user', 'blockchains'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'country' => 'nullable|string|max:2',
            'theme' => 'required|in:light,dark,auto',
            'accent_color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->only(['name', 'email', 'country', 'theme', 'accent_color']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'force_password_change' => false,
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password updated successfully.');
    }

    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        $settings = $request->validate([
            'email_notifications' => 'boolean',
            'new_airdrops' => 'boolean',
            'airdrop_updates' => 'boolean',
            'deadline_reminders' => 'boolean',
            'weekly_digest' => 'boolean',
        ]);

        $user->update(['notification_settings' => $settings]);

        return redirect()->route('profile.index')
            ->with('success', 'Notification preferences updated.');
    }

    public function updateBlockchainPreferences(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'preferred_blockchains' => 'nullable|array',
            'preferred_blockchains.*' => 'exists:blockchains,id',
        ]);

        $user->update([
            'preferred_blockchains' => $request->preferred_blockchains ?? [],
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Blockchain preferences updated.');
    }

    public function setPrimaryWallet(Request $request, UserWallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) {
            abort(403);
        }

        $wallet->markAsPrimary();

        return response()->json([
            'success' => true,
            'message' => 'Primary wallet updated.',
        ]);
    }

    public function removeWallet(UserWallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) {
            abort(403);
        }

        // Prevent removing the last wallet
        if (auth()->user()->wallets()->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the last wallet.',
            ], 400);
        }

        $wallet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wallet removed successfully.',
        ]);
    }
}
