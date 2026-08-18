<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', mobileSidebarOpen: false }"
      x-init="$watch('darkMode', value => value ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); $watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value)); document.documentElement.classList.add('alpine-loaded')">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'Kitchen Manufacturing') : config('app.name', 'Kitchen Manufacturing') }}</title>

        <!-- Prevent FOUC (Flash of Unstyled Content) for dark mode and sidebar -->
        <script>
            // This script runs immediately to prevent white flash and layout shift on page load
            (function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                }

                // Inject CSS to set initial sidebar state and prevent layout shift
                const sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
                const padding = sidebarOpen ? '16rem' : '5rem';
                const width = sidebarOpen ? '16rem' : '5rem';

                const style = document.createElement('style');
                style.id = 'initial-sidebar-state';
                style.textContent = `
                    @media (min-width: 1024px) {
                        .main-content { padding-left: ${padding} !important; }
                        .desktop-sidebar { width: ${width} !important; }
                    }
                `;
                document.head.appendChild(style);

                // Remove the injected style after Alpine loads to allow transitions
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        const styleEl = document.getElementById('initial-sidebar-state');
                        if (styleEl) styleEl.remove();
                    }, 100);
                });
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=optional" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50 dark:bg-black">
            {{-- Mobile sidebar overlay --}}
            <div x-show="mobileSidebarOpen" x-cloak x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-600/75 lg:hidden" @click="mobileSidebarOpen = false"></div>

            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- Main content area --}}
            <div class="main-content transition-all duration-300" :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-20'">
                {{-- Top Navigation Bar --}}
                @include('layouts.topbar')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{-- Flash Messages --}}
                        @if (session('success'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 rounded-md bg-green-50 dark:bg-green-900/50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 rounded-md bg-red-50 dark:bg-red-900/50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
