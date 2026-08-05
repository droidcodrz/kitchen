<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('settings.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dropdown Options') }}
                </h2>
            </div>
            <button x-data @click="$dispatch('open-modal', 'add-option')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Option
            </button>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-md">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-300 p-4 rounded-md">{{ session('error') }}</div>
    @endif

    {{-- Type Tabs --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                @foreach($types as $typeKey => $typeLabel)
                    <a href="{{ route('admin.dropdown-options.index', ['type' => $typeKey]) }}"
                       wire:navigate
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $currentType === $typeKey ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        {{ $typeLabel }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- Options Table --}}
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Value</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($options as $option)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ editing: false }">
                            {{-- View Mode --}}
                            <template x-if="!editing">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $option->sort_order }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $option->value }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $option->label }}</td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($option->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                                    @endif
                                </td>
                            </template>
                            <template x-if="!editing">
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="editing = true" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <x-confirm-delete :action="route('admin.dropdown-options.destroy', $option)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </x-confirm-delete>
                                    </div>
                                </td>
                            </template>

                            {{-- Edit Mode --}}
                            <template x-if="editing">
                                <td colspan="5" class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.dropdown-options.update', $option) }}" class="flex items-center gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="sort_order" value="{{ $option->sort_order }}" class="w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded" min="0">
                                        <input type="text" name="value" value="{{ $option->value }}" required class="w-40 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded">
                                        <input type="text" name="label" value="{{ $option->label }}" required class="w-40 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded">
                                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $option->is_active ? 'checked' : '' }} class="mr-1 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                            Active
                                        </label>
                                        <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition">Save</button>
                                        <button type="button" @click="editing = false" class="px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">Cancel</button>
                                    </form>
                                </td>
                            </template>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No options found for this type. Click "Add Option" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($options->hasPages())
        <div class="mt-6">{{ $options->links() }}</div>
    @endif

    {{-- Add Option Modal --}}
    <x-modal name="add-option" :show="false" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Add Option</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add a new option for <strong>{{ $types[$currentType] ?? $currentType }}</strong></p>

            <form method="POST" action="{{ route('admin.dropdown-options.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="{{ $currentType }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Value <span class="text-red-500">*</span></label>
                    <input type="text" name="value" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm" placeholder="The stored value (e.g. Stainless Steel)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This is the actual value saved in the database.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label <span class="text-red-500">*</span></label>
                    <input type="text" name="label" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm" placeholder="The display label (e.g. Stainless Steel)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This is what users see in the dropdown.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg transition-all">
                        Add Option
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
