<x-app-layout>
    <x-slot name="title">Edit Custom Field</x-slot>

    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.custom-field-definitions.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Custom Field') }}: {{ $definition->field_label }}</h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6 max-w-2xl" x-data="{ fieldType: '{{ old('field_type', $definition->field_type) }}', entityType: '{{ old('entity_type', $definition->entity_type) }}' }">
        <form method="POST" action="{{ route('admin.custom-field-definitions.update', $definition) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <x-input-label for="entity_type" :value="__('Applies To')" />
                    <select id="entity_type" name="entity_type" x-model="entityType" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="product" {{ old('entity_type', $definition->entity_type) === 'product' ? 'selected' : '' }}>Products</option>
                        <option value="inventory_item" {{ old('entity_type', $definition->entity_type) === 'inventory_item' ? 'selected' : '' }}>Inventory Items</option>
                    </select>
                    <x-input-error :messages="$errors->get('entity_type')" class="mt-2" />
                </div>

                <div x-show="entityType !== 'inventory_item'" x-cloak>
                    <x-input-label for="category_id" :value="__('Category (optional) - Products only')" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">All categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $definition->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">For Products, leave blank to show this field for every category, or pick one to limit it.</p>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>
                {{-- Inventory items have no category column: their custom fields
                     are matched on system category instead. Showing the Category
                     selector alone meant an inventory field could not be scoped
                     at all, and whatever was chosen there had no effect. --}}
                <div x-show="entityType === 'inventory_item'" x-cloak>
                    <x-input-label :value="__('System Categories (optional)')" />
                    <div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 border border-gray-300 dark:border-gray-700 rounded-md max-h-48 overflow-y-auto">
                        @foreach($systemCategories ?? [] as $opt)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="applies_to_item_types[]" value="{{ $opt->value }}"
                                    @checked(in_array($opt->value, (array) old('applies_to_item_types', $definition->applies_to_item_types ?? [])))
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>{{ $opt->label }}</span>
                            </label>
                        @endforeach
                        {{-- A system category that was renamed or removed after
                             this field was saved has no checkbox of its own. Left
                             out, it would silently drop off on the next save and
                             widen the field to every inventory item. --}}
                        @foreach(array_diff((array) old('applies_to_item_types', $definition->applies_to_item_types ?? []), collect($systemCategories ?? [])->pluck('value')->all()) as $orphan)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="applies_to_item_types[]" value="{{ $orphan }}" checked
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>{{ $orphan }} (not in list)</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave all unticked to show this field for every inventory item, or pick the system categories it belongs to.</p>
                    <x-input-error :messages="$errors->get('applies_to_item_types')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="field_label" :value="__('Field Label')" />
                    <x-text-input id="field_label" name="field_label" type="text" class="mt-1 block w-full" :value="old('field_label', $definition->field_label)" required />
                    <x-input-error :messages="$errors->get('field_label')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="field_name" :value="__('Field Key')" />
                    <x-text-input id="field_name" name="field_name" type="text" class="mt-1 block w-full font-mono" :value="old('field_name', $definition->field_name)" required />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lowercase letters, numbers, underscores only.</p>
                    <x-input-error :messages="$errors->get('field_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="field_type" :value="__('Field Type')" />
                    <select id="field_type" name="field_type" x-model="fieldType" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea (long text)</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="select">Dropdown (select)</option>
                        <option value="boolean">Yes/No</option>
                    </select>
                    <x-input-error :messages="$errors->get('field_type')" class="mt-2" />
                </div>

                <div x-show="fieldType === 'select'" x-cloak>
                    <x-input-label for="options_text" :value="__('Dropdown Options')" />
                    <textarea id="options_text" name="options_text" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="One option per line">{{ old('options_text', implode("\n", $definition->options ?? [])) }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">One option per line, e.g. Brass, Chrome, Matte Black.</p>
                    <x-input-error :messages="$errors->get('options')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('Sort Order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $definition->sort_order)" />
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center">
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" value="1" {{ old('is_required', $definition->is_required) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required</span>
                    </label>
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $definition->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.custom-field-definitions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
