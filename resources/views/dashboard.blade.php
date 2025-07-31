@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('Dashboard') }}</h1>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subscriptions') }}</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['subscriptions'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Favorites') }}</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['favorites'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Unread Notifications') }}</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['notifications'] }}</p>
        </div>
    </div>

    {{-- Subscribed Airdrops --}}
    @if($subscribed_airdrops->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">{{ __('Your Subscribed Airdrops') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($subscribed_airdrops as $airdrop)
                    <x-airdrop-card :airdrop="$airdrop" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Favorite Airdrops --}}
    @if($favorite_airdrops->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">{{ __('Your Favorite Airdrops') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($favorite_airdrops as $airdrop)
                    <x-airdrop-card :airdrop="$airdrop" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if($subscribed_airdrops->isEmpty() && $favorite_airdrops->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('No airdrops yet') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Start by exploring and subscribing to airdrops.') }}</p>
            <div class="mt-6">
                <a href="{{ route('airdrops.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    {{ __('Explore Airdrops') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection