<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Similar Airdrops</h3>
    
    @if($similarAirdrops && $similarAirdrops->count() > 0)
        <div class="space-y-4">
            @foreach($similarAirdrops as $similarAirdrop)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <a href="{{ route('airdrops.show', $similarAirdrop->slug) }}" class="block">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ $similarAirdrop->name }}
                                </h4>
                                @if($similarAirdrop->project)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $similarAirdrop->project->name }}
                                    </p>
                                @endif
                            </div>
                            
                            @if($similarAirdrop->blockchain)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $similarAirdrop->blockchain->name }}
                                </span>
                            @endif
                        </div>
                        
                        @if($similarAirdrop->description)
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 line-clamp-2">
                                {{ Str::limit($similarAirdrop->description, 100) }}
                            </p>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            <a href="{{ route('airdrops.index', ['blockchain' => $airdrop->blockchain_id ?? null]) }}" 
               class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                View all similar airdrops →
            </a>
        </div>
    @else
        <div class="text-center py-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">No similar airdrops found.</p>
            <a href="{{ route('airdrops.index') }}" 
               class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                Browse all airdrops
            </a>
        </div>
    @endif
</div>
