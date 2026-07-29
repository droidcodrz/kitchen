{{-- Mobile sidebar --}}
<div x-show="mobileSidebarOpen" x-cloak class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 shadow-xl lg:hidden transform transition-transform duration-300" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-800">
        <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Kitchen Mfg</span>
        <button @click="mobileSidebarOpen = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="mt-4 px-2 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
        @include('layouts.sidebar-nav', ['collapsed' => false])
    </nav>
</div>

{{-- Desktop sidebar --}}
<div class="desktop-sidebar hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-30 lg:block bg-white dark:bg-gray-900 shadow-lg border-r border-gray-200 dark:border-gray-800 transition-all duration-300" :class="sidebarOpen ? 'lg:w-64' : 'lg:w-20'">
    {{-- Logo / Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="flex-shrink-0 p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <span x-show="sidebarOpen" x-cloak x-transition class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">Kitchen</span>
        </a>
    </div>

    {{-- Toggle button --}}
    <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full p-1 shadow-md hover:bg-gray-50 dark:hover:bg-gray-700">
        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>

    {{-- Navigation --}}
    <nav class="mt-4 px-2 space-y-1 overflow-y-auto h-[calc(100vh-5rem)]">
        @include('layouts.sidebar-nav', ['collapsed' => false])
    </nav>
</div>
