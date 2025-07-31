<!-- resources/views/components/blockchain-icon.blade.php -->
<div class="flex items-center space-x-2">
    @if($blockchain->logo)
        <img src="{{ asset('storage/' . $blockchain->logo) }}" 
             alt="{{ $blockchain->name }}" 
             class="{{ $component->getIconClass() }} rounded">
    @else
        <div class="{{ $component->getIconClass() }} bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
            <span class="text-primary-600 dark:text-primary-400 font-semibold text-lg">
                {{ $component->getIcon() }}
            </span>
        </div>
    @endif
    
    @if($showName)
        <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $blockchain->name }}
        </span>
    @endif
</div>
