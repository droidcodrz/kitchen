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
                // The account's saved preference is the source of truth: it is what
                // makes the theme follow the user to another browser or machine,
                // where localStorage knows nothing. localStorage is kept in step so
                // the class can still be applied before the first paint.
                const saved = @json(auth()->user()?->theme_preference);
                if (saved === 'dark' || saved === 'light') {
                    localStorage.setItem('darkMode', saved === 'dark' ? 'true' : 'false');
                }

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

            // Records the choice against the account so it survives a different
            // browser or machine. The switch itself has already happened locally,
            // so this is deliberately fire-and-forget: a failure here must not
            // undo or block a theme the user can already see.
            window.saveThemePreference = function (isDark) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (!token) return;
                    fetch(@json(route('settings.update-theme')), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ theme_preference: isDark ? 'dark' : 'light' }),
                    }).catch(function () {});
                } catch (e) {}
            };
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

        <script>
            // A double-clicked submit sends the same form twice. The second
            // insert then collides with the first on a unique column and comes
            // back as a 500, even though the record was created fine - so guard
            // every form once here rather than per form.
            (function () {
                // wire:navigate swaps the body and re-runs this script, but the
                // listeners below live on `document`, which survives the swap.
                // Registering again stacked a second copy: on the next submit
                // the first copy set the flag and the second saw it already set
                // and cancelled the submit as a duplicate. Every form in the app
                // silently stopped working after a single in-app navigation.
                if (window.__kitchenSubmitGuardInstalled) return;
                window.__kitchenSubmitGuardInstalled = true;

                // Longest a submit may hold a form before we assume the
                // navigation is never coming and hand the form back.
                const STUCK_SUBMIT_TIMEOUT_MS = 15000;

                function releaseForm(form) {
                    delete form.dataset.submitting;
                    form.querySelectorAll('button[type=submit], input[type=submit]')
                        .forEach(function (button) {
                            button.disabled = false;
                            button.classList.remove('opacity-75', 'cursor-wait');
                        });
                }

                function guardSubmit(event) {
                    const form = event.target;

                    if (!(form instanceof HTMLFormElement)) return;

                    if (form.dataset.submitting === 'true') {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                        return;
                    }

                    form.dataset.submitting = 'true';

                    // Disable on the next tick: buttons disabled during the
                    // submit event are dropped from the payload, taking any
                    // name/value they carry with them.
                    // cursor-wait, not cursor-not-allowed: the button is busy
                    // finishing this submit, not forbidden. The not-allowed
                    // cursor read as "this action is blocked" and made the
                    // confirm dialogs look broken while they worked.
                    setTimeout(function () {
                        // This guard listens on the capture phase, so it flags
                        // the form before any handler further down gets to
                        // cancel the submit (a duplicate-name check, a client
                        // side validation). When that happens the page stays
                        // put, and a form left flagged swallows every later
                        // click in silence - the button appears dead. Hand it
                        // straight back instead.
                        if (event.defaultPrevented) {
                            releaseForm(form);
                            return;
                        }

                        form.querySelectorAll('button[type=submit], input[type=submit]')
                            .forEach(function (button) {
                                button.disabled = true;
                                button.classList.add('opacity-75', 'cursor-wait');
                            });

                        // Last resort: if the submit never takes the page
                        // anywhere - request dropped, navigation blocked - the
                        // form must not stay jammed for the rest of the visit.
                        setTimeout(function () {
                            if (form.isConnected && form.dataset.submitting === 'true') {
                                releaseForm(form);
                            }
                        }, STUCK_SUBMIT_TIMEOUT_MS);
                    }, 0);
                }

                document.addEventListener('submit', guardSubmit, true);

                // wire:navigate restores pages from cache, including a form
                // still flagged from its last submit - clear it on arrival so
                // the form stays usable.
                function releaseForms() {
                    document.querySelectorAll('form[data-submitting=true]').forEach(releaseForm);
                }

                document.addEventListener('livewire:navigated', releaseForms);
                window.addEventListener('pageshow', releaseForms);
            })();
        </script>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
