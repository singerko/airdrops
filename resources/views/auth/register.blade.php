@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
        <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-6">Create Account</h2>
        
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="name" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                       placeholder="Full Name" value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <input type="email" name="email" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                       placeholder="Email Address" value="{{ old('email') }}">
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
            <div>
                <input type="password" name="password_confirmation" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                       placeholder="Confirm Password">
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                Create Account
            </button>
        </form>
        
        <p class="mt-4 text-center text-sm">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500">Already have an account? Sign in</a>
        </p>
    </div>
</div>
@endsection
