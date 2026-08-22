<x-app-layout>
    <x-slot name="title">Custom Fields</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('settings.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Custom Fields') }}
                </h2>
            </div>
            <a href="{{ route('admin.custom-field-definitions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Field
            </a>
        </div>
    </x-slot>

    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        Add your own fields for Products or Inventory Items -- no developer needed. New fields show up automatically on the create/edit forms.
    </p>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-md">{{ session('success') }}</div>
    @endif

    {{-- Entity Type Tabs --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <a href="{{ route('admin.custom-field-definitions.index', ['entity_type' => 'product']) }}"
                   wire:navigate
                   class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $entityType === 'product' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Products
                </a>
                <a href="{{ route('admin.custom-field-definitions.index', ['entity_type' => 'inventory_item']) }}"
                   wire:navigate
                   class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $entityType === 'inventory_item' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Inventory Items
                </a>
            </nav>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Label</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Key</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Required</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($definitions as $definition)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $definition->field_label }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $definition->field_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($definition->field_type) }}</td>
                            {{-- Inventory fields are scoped by system category, not
                                 by Category, so reading the relationship here showed
                                 every one of them as "All categories" even when it
                                 was limited to one. --}}
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                @if($definition->entity_type === 'inventory_item')
                                    {{ empty($definition->applies_to_item_types)
                                        ? 'All categories'
                                        : implode(', ', array_map(fn ($key) => $systemCategoryLabels[$key] ?? $key, $definition->applies_to_item_types)) }}
                                @else
                                    {{ $definition->category->name ?? 'All categories' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($definition->is_required)
                                    <span class="text-red-500">*</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($definition->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.custom-field-definitions.edit', $definition) }}" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <x-confirm-delete :action="route('admin.custom-field-definitions.destroy', $definition)" message="Delete this field? Any values already saved for it will also be removed.">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </x-confirm-delete>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No custom fields yet for this type. Click "Add Field" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($definitions->hasPages())
        <div class="mt-6">{{ $definitions->links() }}</div>
    @endif
</x-app-layout>
