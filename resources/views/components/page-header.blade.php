<div class="bg-white dark:bg-gray-800 shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    {{ $title ?? 'Page Title' }}
                </h2>
                @if(isset($description))
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
        @if(isset($tabs))
            <div class="mt-6">
                {{ $tabs }}
            </div>
        @endif
    </div>
</div>
