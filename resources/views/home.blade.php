<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Discover Latest Crypto Airdrops')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Discover Latest 
                    <span class="text-blue-600 dark:text-blue-400">Crypto Airdrops</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                    Never miss a crypto airdrop opportunity. Track, manage, and participate in the most promising airdrops across all blockchains.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/airdrops" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                        Explore Airdrops
                    </a>
                    <a href="/register" class="bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_airdrops'] }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Total Airdrops</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_airdrops'] }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Active Airdrops</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_projects'] }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Projects</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['total_blockchains'] }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Blockchains</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Airdrops -->
    @if($featured_airdrops->count() > 0)
    <div class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Featured Airdrops</h2>
                <p class="text-gray-600 dark:text-gray-400">Don't miss these highlighted opportunities</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featured_airdrops as $airdrop)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($airdrop->status == 'active') bg-green-500
                                @elseif($airdrop->status == 'upcoming') bg-blue-500
                                @elseif($airdrop->status == 'ended') bg-gray-500
                                @else bg-yellow-500
                                @endif text-white">
                                {{ ucfirst($airdrop->status) }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $airdrop->blockchain->name ?? 'Unknown' }}</span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $airdrop->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">{{ Str::limit($airdrop->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">{{ $airdrop->project->name ?? 'Unknown Project' }}</span>
                            </div>
                            <a href="/airdrops/{{ $airdrop->slug }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">
                                Learn More →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Airdrops -->
    @if($latest_airdrops->count() > 0)
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Latest Airdrops</h2>
                <a href="/airdrops" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($latest_airdrops as $airdrop)
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">
                                {{ substr($airdrop->project->name ?? 'UN', 0, 2) }}
                            </span>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $airdrop->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    @if($airdrop->status == 'active') bg-green-500
                                    @elseif($airdrop->status == 'upcoming') bg-blue-500
                                    @elseif($airdrop->status == 'ended') bg-gray-500
                                    @else bg-yellow-500
                                    @endif text-white">
                                    {{ ucfirst($airdrop->status) }}
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                                {{ $airdrop->project->name ?? 'Unknown' }} • {{ $airdrop->blockchain->name ?? 'Unknown' }}
                            </p>
                            @if($airdrop->estimated_value)
                                <p class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    ${{ number_format($airdrop->estimated_value, 0) }} estimated value
                                </p>
                            @endif
                        </div>
                        
                        <a href="/airdrops/{{ $airdrop->slug }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Ending Soon -->
    @if($ending_soon->count() > 0)
    <div class="py-16 bg-red-50 dark:bg-red-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Ending Soon ⏰</h2>
                <p class="text-gray-600 dark:text-gray-400">Don't miss these opportunities!</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ending_soon as $airdrop)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-l-4 border-red-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $airdrop->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">{{ $airdrop->project->name ?? 'Unknown' }}</p>
                    <div class="text-red-600 dark:text-red-400 text-sm font-medium mb-3">
                        @if($airdrop->days_until_end !== null)
                            Ends in {{ $airdrop->days_until_end }} days
                        @else
                            End date not set
                        @endif
                    </div>
                    <a href="/airdrops/{{ $airdrop->slug }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">
                        Participate Now
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
