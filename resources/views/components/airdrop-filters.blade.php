<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 p-6 mb-6">
    <form method="GET" action="{{ route('airdrops.index') }}" class="space-y-4">
        <!-- Search Bar -->
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search Airdrops
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        value="{{ $search }}"
                        placeholder="Search by project name, blockchain, or description..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Sort Options -->
            <div class="flex gap-2">
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort By
                    </label>
                    <select 
                        name="sort_by" 
                        id="sort_by"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="updated_at" {{ $sortBy === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="start_date" {{ $sortBy === 'start_date' ? 'selected' : '' }}>Start Date</option>
                        <option value="end_date" {{ $sortBy === 'end_date' ? 'selected' : '' }}>End Date</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Order
                    </label>
                    <select 
                        name="sort_order" 
                        id="sort_order"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Blockchain Filter -->
            <div>
                <label for="blockchains" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Blockchains
                </label>
                <select 
                    name="blockchains[]" 
                    id="blockchains"
                    multiple
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    size="4"
                >
                    @foreach($blockchains as $blockchain)
                        <option 
                            value="{{ $blockchain->id }}" 
                            {{ in_array($blockchain->id, (array)$selectedBlockchains) ? 'selected' : '' }}
                        >
                            {{ $blockchain->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="categories" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Categories
                </label>
                <select 
                    name="categories[]" 
                    id="categories"
                    multiple
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    size="4"
                >
                    @foreach($categories as $category)
                        <option 
                            value="{{ $category->id }}" 
                            {{ in_array($category->id, (array)$selectedCategories) ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select 
                    name="status" 
                    id="status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Statuses</option>
                    <option value="upcoming" {{ $selectedStatus === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="ended" {{ $selectedStatus === 'ended' ? 'selected' : '' }}>Ended</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date Range
                </label>
                <div class="space-y-2">
                    <input 
                        type="date" 
                        name="start_date" 
                        placeholder="Start Date"
                        value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    <input 
                        type="date" 
                        name="end_date" 
                        placeholder="End Date"
                        value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 pt-4 border-t dark:border-gray-600">
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200"
            >
                Apply Filters
            </button>
            
            <a 
                href="{{ route('airdrops.index') }}"
                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200"
            >
                Clear All
            </a>
            
            <button 
                type="button"
                onclick="toggleAdvancedFilters()"
                class="px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200"
            >
                Advanced
            </button>
        </div>
    </form>
</div>

<script>
function toggleAdvancedFilters() {
    // Add advanced filters toggle functionality
    console.log('Advanced filters toggle');
}
</script>
