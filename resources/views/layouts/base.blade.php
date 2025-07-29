<!-- resources/views/layouts/base.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="themeManager()" 
      x-bind:class="{ 'dark': isDark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @stack('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    @yield('body')

    @stack('scripts')
    
    <script>
        function themeManager() {
            return {
                isDark: localStorage.getItem('theme') === 'dark' || 
                       (localStorage.getItem('theme') === null && 
                        window.matchMedia('(prefers-color-scheme: dark)').matches),
                
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                },
                
                init() {
                    window.matchMedia('(prefers-color-scheme: dark)')
                          .addEventListener('change', (e) => {
                        if (localStorage.getItem('theme') === null) {
                            this.isDark = e.matches;
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
