<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\SocialLogin;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        $supportedProviders = ['google', 'facebook', 'twitter'];
        
        if (!in_array($provider, $supportedProviders)) {
            abort(404);
        }
        
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user exists with this social account
            $socialLogin = SocialLogin::where('provider', $provider)
                                    ->where('provider_id', $socialUser->getId())
                                    ->first();

            if ($socialLogin) {
                Auth::login($socialLogin->user);
                return redirect()->intended('/');
            }

            // Check if user exists with this email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }

            // Create social login record
            SocialLogin::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);

            Auth::login($user);
            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed');
        }
    }
}
