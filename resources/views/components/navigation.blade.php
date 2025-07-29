<!-- resources/views/components/navigation.blade.php -->
<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo & Nav Links -->
            <div class="flex items-center">
                <x-logo />
                <x-nav-links />
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <x-theme-toggle />
                <x-search-bar />
                <x-user-menu />
            </div>
        </div>
    </div>
</nav>
