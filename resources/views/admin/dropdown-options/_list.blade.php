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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="optionRow({{ $option->id }})">
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
                                    <div x-show="error" x-cloak class="mb-2 text-xs text-red-600 dark:text-red-400" x-text="error"></div>
                                    <form @submit.prevent="save($event)" class="flex items-center gap-3">
                                        <input type="number" name="sort_order" value="{{ $option->sort_order }}" class="w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded" min="0">
                                        <input type="text" name="value" value="{{ $option->value }}" required class="w-40 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded">
                                        <input type="text" name="label" value="{{ $option->label }}" required class="w-40 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded">
                                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $option->is_active ? 'checked' : '' }} class="mr-1 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                            Active
                                        </label>
                                        <button type="submit" :disabled="submitting" class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition">Save</button>
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

    <script>
    function optionRow(id) {
        return {
            editing: false,
            submitting: false,
            error: '',

            async save(event) {
                this.submitting = true;
                this.error = '';

                const url = '{{ route('admin.dropdown-options.update', ['dropdownOption' => '__ID__']) }}'.replace('__ID__', id);
                const formData = new FormData(event.target);
                formData.append('_method', 'PATCH');

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        this.error = data.message || 'Could not save changes.';
                        this.submitting = false;
                        return;
                    }

                    const listResponse = await fetch(window.location.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const html = await listResponse.text();
                    document.getElementById('options-list').innerHTML = html;
                } catch (e) {
                    this.error = 'Network error. Please try again.';
                    this.submitting = false;
                }
            },
        };
    }
    </script>
