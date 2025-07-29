<!-- resources/views/airdrops/show.blade.php - Using custom directives -->
@extends('layouts.app')

@section('title', $airdrop->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <!-- Airdrop Header -->
            <x-airdrop-header :airdrop="$airdrop" />
            
            <!-- Admin Actions -->
            @admin
                <x-admin-actions :airdrop="$airdrop" />
            @endadmin

            <!-- Description -->
            @if($airdrop->description)
                <x-content-section title="Description">
                    {{ $airdrop->description }}
                </x-content-section>
            @endif

            <!-- Requirements -->
            @if($airdrop->requirements)
                <x-content-section title="Requirements">
                    {{ $airdrop->requirements }}
                </x-content-section>
            @endif

            <!-- Phases -->
            @if($airdrop->phases->count() > 0)
                <x-airdrop-phases :phases="$airdrop->phases" />
            @endif
        </div>

        <div class="lg:col-span-1">
            <!-- Quick Info -->
            <x-airdrop-sidebar :airdrop="$airdrop" />
            
            <!-- Similar Airdrops -->
            @if($similar_airdrops->count() > 0)
                <x-similar-airdrops :airdrops="$similar_airdrops" />
            @endif
        </div>
    </div>
</div>
@endsection

<!-- Usage with custom helper functions -->
<div class="stats">
    <span>@number($airdrop->views_count) views</span>
    <span>@timeago($airdrop->created_at)</span>
    <span>@blockchainIcon($airdrop->blockchain->slug) {{ $airdrop->blockchain->name }}</span>
</div>

<!-- Wallet-specific content -->
@wallet($airdrop->blockchain_id)
    <div class="wallet-connected">
        You have a {{ $airdrop->blockchain->name }} wallet connected!
    </div>
@endwallet
