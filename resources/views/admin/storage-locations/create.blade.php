<x-app-layout>
    <x-slot name="title">Add Storage Location</x-slot>

    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.storage-locations.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Add Storage Location') }}</h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.storage-locations.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="code" value="Code" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm" rows="3">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <x-primary-button>Create Location</x-primary-button>
                <a href="{{ route('admin.storage-locations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
