<!-- resources/views/projects/index.blade.php -->
@extends('layouts.app')

@section('title', 'All Projects')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">All Projects</h1>
        <p class="text-gray-600 dark:text-gray-400">Explore cryptocurrency projects offering airdrops</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search projects..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    >
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <select name="sort_by" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="latest" {{ request('sort_by') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="popular" {{ request('sort_by') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="alphabetical" {{ request('sort_by') === 'alphabetical' ? 'selected' : '' }}>A-Z</option>
                        <option value="most_airdrops" {{ request('sort_by') === 'most_airdrops' ? 'selected' : '' }}>Most Airdrops</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Apply Filters
                </button>
                <a href="{{ route('projects.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Clear All
                </a>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($projects as $project)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-200 overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-start space-x-4 mb-4">
                            @if($project->getLogoUrlAttribute())
                                <img src="{{ $project->getLogoUrlAttribute() }}" alt="{{ $project->name }}" class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                    <span class="text-primary-600 dark:text-primary-400 font-semibold text-lg">{{ substr($project->name, 0, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $project->name }}</h3>
                                    @if($project->is_verified)
                                        <span class="text-blue-500" title="Verified Project">✓</span>
                                    @endif
                                </div>
                                @if($project->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $project->category->color }}20; color: {{ $project->category->color }};">
                                        {{ $project->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @if($project->description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">{{ Str::limit($project->description, 150) }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-1">
                                @if($project->rating > 0)
                                    <span class="text-yellow-500">⭐</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $project->rating }} ({{ $project->rating_count }})</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $project->airdrops_count }} airdrops</span>
                        </div>

                        <!-- Social Links -->
                        @if($project->getSocialLinksAttribute())
                            <div class="flex items-center space-x-2 mb-4">
                                @foreach($project->getSocialLinksAttribute() as $platform => $url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        @if($platform === 'website')
                                            🌐
                                        @elseif($platform === 'twitter')
                                            🐦
                                        @elseif($platform === 'discord')
                                            💬
                                        @elseif($platform === 'telegram')
                                            ✈️
                                        @elseif($platform === 'github')
                                            🔗
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('projects.show', $project->slug) }}" class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white py-2 rounded-lg font-medium transition-colors duration-200">
                            View Project
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @else
        <!-- No Results -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No projects found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search criteria.</p>
        </div>
    @endif
</div>
@endsection
