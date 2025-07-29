@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <nav class="space-y-1" x-data="{ activeTab: '{{ request('tab', 'profile') }}' }">
                <a href="#" @click.prevent="activeTab = 'profile'" 
                   :class="activeTab === 'profile' ? 'bg-primary-50 dark:bg-primary-900/50 border-primary-500 text-primary-700 dark:text-primary-300' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                   class="border-l-4 pl-3 py-2 text-sm font-medium block">
                    Profile Information
                </a>
                <a href="#" @click.prevent="activeTab = 'wallets'" 
                   :class="activeTab === 'wallets' ? 'bg-primary-50 dark:bg-primary-900/50 border-primary-500 text-primary-700 dark:text-primary-300' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                   class="border-l-4 pl-3 py-2 text-sm font-medium block">
                    Connected Wallets
                </a>
                <a href="#" @click.prevent="activeTab = 'notifications'" 
                   :class="activeTab === 'notifications' ? 'bg-primary-50 dark:bg-primary-900/50 border-primary-500 text-primary-700 dark:text-primary-300' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                   class="border-l-4 pl-3 py-2 text-sm font-medium block">
                    Notifications
                </a>
                <a href="#" @click.prevent="activeTab = 'subscriptions'" 
                   :class="activeTab === 'subscriptions' ? 'bg-primary-50 dark:bg-primary-900/50 border-primary-500 text-primary-700 dark:text-primary-300' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                   class="border-l-4 pl-3 py-2 text-sm font-medium block">
                    Subscriptions
                </a>
                <a href="#" @click.prevent="activeTab = 'security'" 
                   :class="activeTab === 'security' ? 'bg-primary-50 dark:bg-primary-900/50 border-primary-500 text-primary-700 dark:text-primary-300' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                   class="border-l-4 pl-3 py-2 text-sm font-medium block">
                    Security
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3" x-data="{ activeTab: '{{ request('tab', 'profile') }}' }">
            <!-- Profile Information -->
            <div x-show="activeTab === 'profile'" class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Information</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Update your account's profile information and email address.</p>
                </div>
                
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Avatar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Avatar</label>
                        <div class="flex items-center space-x-4">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div class="w-16 h-16 bg-primary-500 rounded-full flex items-center justify-center">
                                    <span class="text-xl font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <input type="file" name="avatar" accept="image/*" class="text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Theme Preference -->
                    <div>
                        <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Theme Preference</label>
                        <select name="theme" id="theme" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="auto" {{ $user->theme === 'auto' ? 'selected' : '' }}>Auto (System)</option>
                            <option value="light" {{ $user->theme === 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $user->theme === 'dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                    </div>

                    <!-- Accent Color -->
                    <div>
                        <label for="accent_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Accent Color</label>
                        <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', $user->accent_color) }}"
                               class="w-16 h-10 border border-gray-300 dark:border-gray-600 rounded-md">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Connected Wallets -->
            <div x-show="activeTab === 'wallets'" class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Connected Wallets</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your connected cryptocurrency wallets.</p>
                </div>
                
                <div class="p-6">
                    @if($user->wallets->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($user->wallets as $wallet)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                            <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ substr($wallet->blockchain->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $wallet->blockchain->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $wallet->getShortAddressAttribute() }}</div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ ucfirst($wallet->wallet_type) }}</div>
                                        </div>
                                        @if($wallet->is_primary)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if(!$wallet->is_primary)
                                            <button data-set-primary="{{ $wallet->id }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm">
                                                Set Primary
                                            </button>
                                        @endif
                                        @if($user->wallets->count() > 1)
                                            <button data-remove-wallet="{{ $wallet->id }}" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Connect New Wallet -->
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Connect a new wallet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a blockchain to connect your wallet</p>
                        
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($blockchains as $blockchain)
                                <div data-blockchain="{{ $blockchain->slug }}" data-wallet-list class="space-y-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $blockchain->name }}</h4>
                                    <!-- Wallet buttons will be populated by JavaScript -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div x-show="activeTab === 'notifications'" class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notification Preferences</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure how you want to receive notifications.</p>
                </div>
                
                <form method="POST" action="{{ route('profile.notifications.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Receive notifications via email</p>
                            </div>
                            <input type="checkbox" name="email_notifications" value="1" 
                                   {{ ($user->notification_settings['email_notifications'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-white">New Airdrops</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about new airdrops on your preferred blockchains</p>
                            </div>
                            <input type="checkbox" name="new_airdrops" value="1" 
                                   {{ ($user->notification_settings['new_airdrops'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Airdrop Updates</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Receive updates for subscribed airdrops</p>
                            </div>
                            <input type="checkbox" name="airdrop_updates" value="1" 
                                   {{ ($user->notification_settings['airdrop_updates'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Deadline Reminders</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get reminded before airdrops end</p>
                            </div>
                            <input type="checkbox" name="deadline_reminders" value="1" 
                                   {{ ($user->notification_settings['deadline_reminders'] ?? true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Weekly Digest</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Receive a weekly summary of new opportunities</p>
                            </div>
                            <input type="checkbox" name="weekly_digest" value="1" 
                                   {{ ($user->notification_settings['weekly_digest'] ?? false) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium">
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>

            <!-- Subscriptions -->
            <div x-show="activeTab === 'subscriptions'" class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">My Subscriptions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Airdrops you're following for updates.</p>
                </div>
                
                <div class="p-6">
                    @if($user->subscriptions->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->subscriptions as $subscription)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $subscription->airdrop->title }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->airdrop->project->name }} • {{ $subscription->airdrop->blockchain->name }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $subscription->airdrop->getStatusBadgeClass() }} text-white">
                                            {{ ucfirst($subscription->airdrop->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('airdrops.show', $subscription->airdrop->slug) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm">
                                            View
                                        </a>
                                        <button data-airdrop-subscribe data-airdrop-id="{{ $subscription->airdrop->id }}" data-subscribed="true" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm">
                                            Unsubscribe
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 110-15H9.5a7.5 7.5 0 110 15z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No subscriptions</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start following airdrops to get notified about updates.</p>
                            <div class="mt-6">
                                <a href="{{ route('airdrops.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                    Browse Airdrops
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Security -->
            <div x-show="activeTab === 'security'" class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your account security and password.</p>
                </div>
                
                <form method="POST" action="{{ route('profile.password.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if($user->password)
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/wallet-integration.js') }}"></script>
@endpush
@endsection
