<div class="bg-white dark:bg-gray-800 rounded-lg shadow {{ $class }}">
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            @if($title)
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="p-6">
        @if($content)
            <div class="prose dark:prose-invert max-w-none">
                {!! $content !!}
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
