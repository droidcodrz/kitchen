<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' . $brandName : $brandName }}</title>

        <!-- Prevent FOUC (Flash of Unstyled Content) for dark mode -->
        <script>
            // This script runs immediately to prevent white flash on page load
            (function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        {{-- Figtree was pulled from an external CDN here, but the theme sets
             Inter and nothing ever referenced Figtree. The request was blocked
             by our own Content-Security-Policy anyway, so every visit to the
             login page logged a CSP error for a font it did not use. --}}

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/" class="flex items-center space-x-2">
                    <x-app-logo class="w-12 h-12 text-indigo-600 dark:text-indigo-400" />
                    <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $brandName }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
