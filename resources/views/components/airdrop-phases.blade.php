@if($phases && $phases->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Airdrop Phases</h3>
        
        <div class="space-y-4">
            @foreach($phases as $phase)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 
                    {{ $phase->is_active ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' : '' }}">
                    
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ $phase->name }}
                            @if($phase->is_active)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Active
                                </span>
                            @endif
                        </h4>
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($phase->start_date)
                                {{ $phase->start_date->format('M j, Y') }}
                            @endif
                            @if($phase->start_date && $phase->end_date)
                                -
                            @endif
                            @if($phase->end_date)
                                {{ $phase->end_date->format('M j, Y') }}
                            @endif
                        </div>
                    </div>
                    
                    @if($phase->description)
                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                            {{ $phase->description }}
                        </p>
                    @endif
                    
                    @if($phase->requirements)
                        <div class="mt-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Requirements:</span>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $phase->requirements }}
                            </p>
                        </div>
                    @endif
                    
                    @if($phase->reward_amount)
                        <div class="mt-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reward:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white ml-1">
                                {{ $phase->reward_amount }} {{ $phase->reward_token ?? 'tokens' }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Airdrop Phases</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm">No phases defined for this airdrop.</p>
    </div>
@endif
