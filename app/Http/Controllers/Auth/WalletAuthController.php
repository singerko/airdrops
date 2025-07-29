<?php
// app/Http/Controllers/Auth/WalletAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Blockchain;
use App\Services\WalletAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletAuthController extends Controller
{
    protected $walletAuthService;

    public function __construct(WalletAuthService $walletAuthService)
    {
        $this->walletAuthService = $walletAuthService;
    }

    public function connect(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'blockchain_slug' => 'required|string',
            'wallet_type' => 'required|string',
            'signature' => 'required|string',
            'message' => 'required|string',
        ]);

        $blockchain = Blockchain::where('slug', $request->blockchain_slug)->firstOrFail();

        // Verify signature
        $isValid = $this->walletAuthService->verifySignature(
            $request->address,
            $request->message,
            $request->signature,
            $request->blockchain_slug
        );

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 400);
        }

        // Find or create user
        $wallet = UserWallet::where('address', $request->address)
            ->where('blockchain_id', $blockchain->id)
            ->first();

        if ($wallet) {
            // Existing wallet - login user
            $user = $wallet->user;
            Auth::login($user);
            
            $user->update(['last_login_at' => now()]);
        } else {
            // New wallet - create user
            $user = User::create([
                'name' => 'User ' . Str::random(6),
                'email' => strtolower($request->address) . '@wallet.local',
                'email_verified_at' => now(),
            ]);

            // Create wallet record
            UserWallet::create([
                'user_id' => $user->id,
                'blockchain_id' => $blockchain->id,
                'address' => $request->address,
                'wallet_type' => $request->wallet_type,
                'is_primary' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            Auth::login($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully connected wallet.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function disconnect(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'blockchain_slug' => 'required|string',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.',
            ], 401);
        }

        $blockchain = Blockchain::where('slug', $request->blockchain_slug)->firstOrFail();
        
        $wallet = auth()->user()->wallets()
            ->where('address', $request->address)
            ->where('blockchain_id', $blockchain->id)
            ->first();

        if ($wallet) {
            // If this is the last wallet, don't allow disconnection
            if (auth()->user()->wallets()->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot disconnect the last wallet.',
                ], 400);
            }

            $wallet->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Wallet disconnected successfully.',
        ]);
    }

    public function getNonce(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        $nonce = $this->walletAuthService->generateNonce($request->address);

        return response()->json([
            'nonce' => $nonce,
            'message' => $this->walletAuthService->generateMessage($request->address, $nonce),
        ]);
    }
}
