<!-- resources/views/airdrops/index.blade.php - Refactored -->
@extends('layouts.app')

@section('title', 'All Airdrops')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <x-page-header title="All Airdrops" 
                   subtitle="Discover the latest cryptocurrency airdrop opportunities" />

    <!-- Filters -->
    <x-airdrop-filters :blockchains="$blockchains" 
                       :categories="$categories"
                       :status-options="$statusOptions" />

    <!-- Results -->
    @if($airdrops->count() > 0)
        <!-- Results Header -->
        <x-results-header :paginator="$airdrops" />

        <!-- Airdrop Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($airdrops as $airdrop)
                <x-airdrop-card :airdrop="$airdrop" 
                               :is-subscribed="false"
                               :is-favorited="false" />
            @endforeach
        </div>

        <!-- Pagination -->
        {{ $airdrops->links('components.pagination') }}
    @else
        <x-empty-state title="No airdrops found"
                       message="Try adjusting your filters or search criteria."
                       :action="['url' => route('airdrops.index'), 'label' => 'Clear Filters']" />
    @endif
</div>
@endsection
