<!-- resources/views/components/data-table.blade.php -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            @if($sortable && isset($column['sortable']) && $column['sortable'])
                                <button class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                                    <span>{{ $column['label'] }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                    
                    @if(!empty($actions))
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(isset($column['component']))
                                    <x-dynamic-component 
                                        :component="$column['component']" 
                                        :data="data_get($row, $column['field'])"
                                        :row="$row" 
                                    />
                                @else
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ data_get($row, $column['field']) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                        
                        @if(!empty($actions))
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @foreach($actions as $action)
                                        @if(isset($action['condition']) && !$action['condition']($row))
                                            @continue
                                        @endif
                                        
                                        @if($action['type'] === 'link')
                                            <a href="{{ $action['url']($row) }}" 
                                               class="{{ $action['class'] ?? 'text-primary-600 hover:text-primary-900' }}">
                                                {{ $action['label'] }}
                                            </a>
                                        @elseif($action['type'] === 'form')
                                            <form method="POST" 
                                                  action="{{ $action['url']($row) }}" 
                                                  class="inline-block"
                                                  @if($action['confirm'] ?? false)
                                                      onsubmit="return confirm('{{ $action['confirm'] }}')"
                                                  @endif>
                                                @csrf
                                                @if($action['method'] ?? 'POST' !== 'POST')
                                                    @method($action['method'])
                                                @endif
                                                <button type="submit" 
                                                        class="{{ $action['class'] ?? 'text-red-600 hover:text-red-900' }}">
                                                    {{ $action['label'] }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" 
                            class="px-6 py-12 text-center">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="text-sm font-medium">No data available</h3>
                                <p class="mt-1 text-sm">There are no records to display.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
