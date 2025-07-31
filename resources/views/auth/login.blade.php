@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Sign in to Airdrop Portal</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Choose your preferred login method</p>
        </div>
        
        <!-- Wallet Authentication -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Connect Wallet</h3>
            <div class="space-y-3">
                <button onclick="connectMetaMask()" 
                        class="w-full flex items-center justify-center py-2 px-4 border border-orange-500 rounded-md shadow-sm bg-orange-500 text-white hover:bg-orange-600">
                    🦊 MetaMask
                </button>
                <button onclick="connectPhantom()" 
                        class="w-full flex items-center justify-center py-2 px-4 border border-purple-500 rounded-md shadow-sm bg-purple-500 text-white hover:bg-purple-600">
                    👻 Phantom
                </button>
                <button onclick="connectKeplr()" 
                        class="w-full flex items-center justify-center py-2 px-4 border border-blue-500 rounded-md shadow-sm bg-blue-500 text-white hover:bg-blue-600">
                    ⚛️ Keplr
                </button>
            </div>
        </div>

        <!-- Email/Password Login -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Email & Password</h3>
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <input type="email" name="email" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                           placeholder="Email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input type="password" name="password" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                           placeholder="Password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-sm text-gray-600 dark:text-gray-400">Remember me</label>
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    Sign In
                </button>
            </form>
            <p class="mt-4 text-center text-sm">
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500">Don't have an account? Register</a>
            </p>
        </div>
        
        <!-- Social Login -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Social Login</h3>
            <div class="space-y-3">
                <a href="{{ route('social.redirect', 'google') }}" 
                   class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                    🔍 Continue with Google
                </a>
                <a href="{{ route('social.redirect', 'facebook') }}" 
                   class="w-full flex justify-center py-2 px-4 border border-blue-600 rounded-md shadow-sm bg-blue-600 text-white hover:bg-blue-700">
                    📘 Continue with Facebook
                </a>
                <a href="{{ route('social.redirect', 'twitter') }}" 
                   class="w-full flex justify-center py-2 px-4 border border-gray-900 rounded-md shadow-sm bg-gray-900 text-white hover:bg-gray-800">
                    🐦 Continue with X (Twitter)
                </a>
            </div>
        </div>
    </div>
</div>

<script>
async function connectMetaMask() {
    if (typeof window.ethereum !== 'undefined') {
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            // Implement wallet verification logic
            console.log('MetaMask connected:', accounts[0]);
        } catch (error) {
            console.error('MetaMask connection failed:', error);
        }
    } else {
        alert('MetaMask is not installed');
    }
}

async function connectPhantom() {
    // Phantom wallet logic
    console.log('Phantom wallet connection not implemented yet');
}

async function connectKeplr() {
    // Keplr wallet logic  
    console.log('Keplr wallet connection not implemented yet');
}
</script>
@endsection
