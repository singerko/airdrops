<div class="text-center py-12">
    <div class="mx-auto h-24 w-24 text-gray-400">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $title ?? 'No items found' }}</h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description ?? 'Get started by creating a new item.' }}</p>
	@if(isset($action))
		<div class="mt-6">
			@if(is_array($action) && isset($action['url']) && isset($action['label']))
				<a href="{{ $action['url'] }}" 
				class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
					{{ $action['label'] }}
				</a>
			@elseif(is_string($action))
				{!! $action !!}
			@endif
		</div>
	@endif
</div>
