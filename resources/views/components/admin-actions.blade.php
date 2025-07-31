@if(!empty($actions))
    <div class="flex items-center space-x-2">
        @foreach($actions as $key => $action)
            @if($key === 'delete' && isset($action['confirm']) && $action['confirm'])
                <form method="POST" action="{{ $action['url'] }}" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this item?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="{{ $action['class'] ?? 'text-red-600 hover:text-red-900' }}">
                        {{ $action['label'] }}
                    </button>
                </form>
            @else
                <a href="{{ $action['url'] }}" class="{{ $action['class'] ?? 'text-blue-600 hover:text-blue-900' }}">
                    {{ $action['label'] }}
                </a>
            @endif
        @endforeach
    </div>
@endif
