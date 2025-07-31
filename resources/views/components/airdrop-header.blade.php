<div class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        @if(!empty($breadcrumbs))
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="inline-flex items-center">
                            @if(!$loop->last)
                                <a href="{{ $breadcrumb['url'] }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    {{ $breadcrumb['label'] }}
                                </a>
                                <svg class="w-6 h-6 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <span class="text-gray-700 dark:text-gray-300">{{ $breadcrumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <!-- Header Content -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                    {{ $title }}
                </h1>
                @if($description)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $description }}
                    </p>
                @endif
                
                <!-- Airdrop specific info -->
                @if($airdrop)
                    <div class="mt-4 flex flex-wrap gap-4">
                        @if($airdrop->blockchain)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $airdrop->blockchain->name }}
                            </span>
                        @endif
                        
                        @if($airdrop->project)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $airdrop->project->name }}
                            </span>
                        @endif
                        
                        @if($airdrop->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($airdrop->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($airdrop->status === 'upcoming') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($airdrop->status === 'ended') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ ucfirst($airdrop->status) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-4 flex md:mt-0 md:ml-4">
                @if($airdrop && $airdrop->website_url)
                    <a href="{{ $airdrop->website_url }}" target="_blank" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Visit Website
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
