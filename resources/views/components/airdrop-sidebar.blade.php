<div class="space-y-6">
    <!-- Quick Info Card -->
    @if($airdrop)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Info</h3>
            
            <dl class="space-y-3">
                @if($airdrop->blockchain)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Blockchain</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $airdrop->blockchain->name }}</dd>
                    </div>
                @endif
                
                @if($airdrop->project)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Project</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $airdrop->project->name }}</dd>
                    </div>
                @endif
                
                @if($airdrop->start_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $airdrop->start_date->format('M j, Y') }}</dd>
                    </div>
                @endif
                
                @if($airdrop->end_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $airdrop->end_date->format('M j, Y') }}</dd>
                    </div>
                @endif
                
                @if($airdrop->total_supply)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Supply</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ number_format($airdrop->total_supply) }} tokens</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

    <!-- Actions Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
        
        <div class="space-y-3">
            @if($airdrop && $airdrop->website_url)
                <a href="{{ $airdrop->website_url }}" target="_blank" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Visit Project Website
                </a>
            @endif
            
            @if($airdrop && $airdrop->twitter_url)
                <a href="{{ $airdrop->twitter_url }}" target="_blank" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Follow on Twitter
                </a>
            @endif
            
            @if($airdrop && $airdrop->discord_url)
                <a href="{{ $airdrop->discord_url }}" target="_blank" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Join Discord
                </a>
            @endif
            
            @auth
                <button onclick="toggleFavorite()" 
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-700 dark:hover:bg-yellow-900/30">
                    ⭐ Add to Favorites
                </button>
            @endauth
        </div>
    </div>

    <!-- Related Airdrops -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Related Airdrops</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Similar airdrops will appear here.</p>
    </div>
</div>

<script>
function toggleFavorite() {
    // Add favorite toggle functionality
    console.log('Toggle favorite functionality not implemented yet');
}
</script>
