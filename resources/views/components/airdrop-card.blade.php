<!-- resources/views/components/airdrop-card.blade.php -->
<div class="card hover:shadow-lg transition-shadow duration-200 overflow-hidden">
    @if($airdrop->getFeaturedImageUrl())
        <img src="{{ $airdrop->getFeaturedImageUrl() }}" 
             alt="{{ $airdrop->title }}" 
             class="w-full h-48 object-cover">
    @endif
    
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <x-status-badge :status="$airdrop->status" />
            
            <div class="flex items-center space-x-2">
                @if($showActions && auth()->check())
                    <button 
                        data-airdrop-favorite 
                        data-airdrop-id="{{ $airdrop->id }}"
                        data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
                        class="text-gray-400 hover:text-red-500 transition-colors duration-200"
                        title="Add to favorites"
                    >
                        <svg class="w-5 h-5 {{ $isFavorited ? 'text-red-500 fill-current' : '' }}" 
                             fill="{{ $isFavorited ? 'currentColor' : 'none' }}" 
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </button>
                @endif
                
                <x-blockchain-icon :blockchain="$airdrop->blockchain" size="sm" />
            </div>
        </div>
        
        <!-- Title & Description -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            {{ $airdrop->title }}
        </h3>
        
        @if($airdrop->description)
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                {{ $airdrop->description }}
            </p>
        @endif
        
        <!-- Meta Info -->
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span class="font-medium">{{ $airdrop->project->name }}</span>
            </div>
            
            @if($airdrop->estimated_value)
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    ${{ number_format($airdrop->estimated_value, 0) }}
                </span>
            @endif
        </div>

        <!-- Rating & Views -->
        <div class="flex items-center justify-between mb-4">
            @if($airdrop->rating > 0)
                <div class="flex items-center">
                    <span class="text-yellow-500">⭐</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">
                        {{ $airdrop->rating }} ({{ $airdrop->rating_count }})
                    </span>
                </div>
            @else
                <div></div>
            @endif
            
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ number_format($airdrop->views_count) }} views
            </span>
        </div>

        <!-- Time Remaining -->
        @if($airdrop->isActive() && $this->getDaysRemaining())
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-orange-700 dark:text-orange-300 text-sm font-medium">
                        {{ $this->getDaysRemaining() }} days remaining
                    </span>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between">
            @if($showActions && auth()->check())
                <button 
                    data-airdrop-subscribe 
                    data-airdrop-id="{{ $airdrop->id }}"
                    data-subscribed="{{ $isSubscribed ? 'true' : 'false' }}"
                    class="btn {{ $isSubscribed ? 'btn-secondary' : 'btn-primary' }} text-sm"
                >
                    {{ $isSubscribed ? 'Subscribed' : 'Subscribe' }}
                </button>
            @else
                <div></div>
            @endif
            
            <a href="{{ route('airdrops.show', $airdrop->slug) }}" 
               class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium text-sm">
                Learn More →
            </a>
        </div>
    </div>
</div>
