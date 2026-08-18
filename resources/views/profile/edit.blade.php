<x-app-layout>
    <x-slot name="title">Profile</x-slot>

    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('settings.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-800 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div id="update-password" class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-800 sm:rounded-lg scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-800 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <script>
        // Jump straight to and focus the password section when arriving via
        // the Settings page "Change Password" link (#update-password), instead
        // of landing at the top of the whole Profile page. Bound to both a
        // genuine first load and livewire:navigated (wire:navigate SPA nav) -
        // DOMContentLoaded alone only fires once and never again on SPA
        // navigation between pages.
        function focusPasswordSectionIfLinked() {
            if (window.location.hash === '#update-password') {
                const section = document.getElementById('update-password');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    const firstInput = section.querySelector('input');
                    if (firstInput) firstInput.focus();
                }
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', focusPasswordSectionIfLinked);
        } else {
            focusPasswordSectionIfLinked();
        }
        document.addEventListener('livewire:navigated', focusPasswordSectionIfLinked);
    </script>
</x-app-layout>
