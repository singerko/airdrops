@php
    $range = $component->getResultsRange();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 p-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        
        <!-- Results Info & Active Filters -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <!-- Results Count -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    @if($totalResults > 0)
                        Showing <span class="font-medium text-gray-900 dark:text-white">{{ number_format($range['start']) }}-{{ number_format($range['end']) }}</span> 
                        of <span class="font-medium text-gray-900 dark:text-white">{{ number_format($totalResults) }}</span> airdrops
                    @else
                        <span class="text-gray-500 dark:text-gray-400">No airdrops found</span>
                    @endif
                </div>

                <!-- Active Filters Pills -->
                @if($component->hasActiveFilters())
                    <div class="flex flex-wrap gap-2">
                        @if(!empty($searchQuery))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Search: "{{ Str::limit($searchQuery, 20) }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        @foreach($appliedFilters as $filterType => $filterValue)
                            @if(!empty($filterValue))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ ucfirst($filterType) }}: {{ is_array($filterValue) ? implode(', ', $filterValue) : $filterValue }}
                                    <a href="{{ request()->fullUrlWithQuery([$filterType => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endforeach

                        <a href="{{ url()->current() }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                            Clear All Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- View Controls -->
        <div class="flex items-center gap-4">
            
            <!-- Quick Sort -->
            <div class="flex items-center gap-2">
                <label for="quick-sort" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Sort:
                </label>
                <select 
                    id="quick-sort" 
                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    onchange="updateSort(this.value)"
                >
                    <option value="created_at-desc" {{ $sortBy === 'created_at' && $sortOrder === 'desc' ? 'selected' : '' }}>
                        Newest First
                    </option>
                    <option value="created_at-asc" {{ $sortBy === 'created_at' && $sortOrder === 'asc' ? 'selected' : '' }}>
                        Oldest First
                    </option>
                    <option value="updated_at-desc" {{ $sortBy === 'updated_at' && $sortOrder === 'desc' ? 'selected' : '' }}>
                        Recently Updated
                    </option>
                    <option value="name-asc" {{ $sortBy === 'name' && $sortOrder === 'asc' ? 'selected' : '' }}>
                        Name A-Z
                    </option>
                    <option value="name-desc" {{ $sortBy === 'name' && $sortOrder === 'desc' ? 'selected' : '' }}>
                        Name Z-A
                    </option>
                    <option value="start_date-asc" {{ $sortBy === 'start_date' && $sortOrder === 'asc' ? 'selected' : '' }}>
                        Starting Soon
                    </option>
                    <option value="end_date-asc" {{ $sortBy === 'end_date' && $sortOrder === 'asc' ? 'selected' : '' }}>
                        Ending Soon
                    </option>
                </select>
            </div>

            <!-- View Mode Toggle -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button 
                    type="button"
                    onclick="changeViewMode('grid')"
                    class="view-mode-btn {{ $viewMode === 'grid' ? 'active' : '' }} p-2 rounded-md transition-colors"
                    data-mode="grid"
                    title="Grid View"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button 
                    type="button"
                    onclick="changeViewMode('list')"
                    class="view-mode-btn {{ $viewMode === 'list' ? 'active' : '' }} p-2 rounded-md transition-colors"
                    data-mode="list"
                    title="List View"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Results Per Page -->
            <div class="flex items-center gap-2">
                <label for="per-page" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Show:
                </label>
                <select 
                    id="per-page" 
                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    onchange="updatePerPage(this.value)"
                >
                    <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>12</option>
                    <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>24</option>
                    <option value="48" {{ $perPage == 48 ? 'selected' : '' }}>48</option>
                    <option value="96" {{ $perPage == 96 ? 'selected' : '' }}>96</option>
                </select>
            </div>
        </div>
    </div>
</div>

<style>
.view-mode-btn {
    @apply text-gray-500;
}
.view-mode-btn.active {
    @apply bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm;
}
.view-mode-btn:not(.active):hover {
    @apply text-gray-700 dark:text-gray-300;
}
</style>

<script>
function updateSort(value) {
    const [sortBy, sortOrder] = value.split('-');
    const url = new URL(window.location);
    url.searchParams.set('sort_by', sortBy);
    url.searchParams.set('sort_order', sortOrder);
    url.searchParams.set('page', '1'); // Reset to first page
    window.location.href = url.toString();
}

function updatePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', '1'); // Reset to first page
    window.location.href = url.toString();
}

function changeViewMode(mode) {
    const url = new URL(window.location);
    url.searchParams.set('view', mode);
    window.location.href = url.toString();
}

// Update active state for view mode buttons
document.addEventListener('DOMContentLoaded', function() {
    const currentViewMode = new URLSearchParams(window.location.search).get('view') || 'grid';
    document.querySelectorAll('.view-mode-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.mode === currentViewMode) {
            btn.classList.add('active');
        }
    });
});
</script>
