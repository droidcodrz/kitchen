<x-app-layout>
    <x-slot name="title">Add Product</x-slot>

    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add Product') }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg" x-data="createProductForm()">
        {{-- Live Preview --}}
        <div class="mx-6 mt-6 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Item Label (Auto)</p>
                    <p class="text-sm font-bold text-blue-700 dark:text-blue-400 font-mono truncate" x-text="generatedLabel || 'Not generated yet...'"></p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Description (Auto)</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100 truncate" x-text="generatedDescription || 'Select fields below...'"></p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Custom Name / Suffix --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Custom Name (optional)')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="Leave blank to use the auto-generated description" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If entered, this replaces the auto-generated description above.</p>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="name_suffix" :value="__('Name Suffix (optional)')" />
                    <x-text-input id="name_suffix" name="name_suffix" type="text" class="mt-1 block w-full" :value="old('name_suffix')" placeholder="e.g. Type 1, Version A, Brass Handle" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Appended to the name to tell near-identical variants apart.</p>
                    <x-input-error :messages="$errors->get('name_suffix')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Row 1: Item Type, Material Type, Material Grade --}}
                <div>
                    <x-input-label for="item_type" :value="__('Item Type')" />
                    <select id="item_type" name="item_type" x-model="itemType" @change="updatePreview(); loadCustomFields();" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select item type</option>
                        @foreach($dropdownOptions['item_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('item_type') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('item_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="material_type" :value="__('Material Type')" />
                    <select id="material_type" name="material_type" x-model="materialType" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select material type</option>
                        @foreach($dropdownOptions['material_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('material_type') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="material_grade" :value="__('Material Grade')" />
                    <select id="material_grade" name="material_grade" x-model="materialGrade" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">No grade</option>
                        @foreach($dropdownOptions['material_grades'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('material_grade') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('material_grade')" class="mt-2" />
                </div>

                {{-- Row 2: Gauge, Thickness mm, Dimension --}}
                <div>
                    <x-input-label for="thickness_gauge" :value="__('Thickness (Gauge)')" />
                    <select id="thickness_gauge" name="thickness_gauge" x-model="thicknessGauge" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">No gauge</option>
                        @foreach($dropdownOptions['thickness_gauges'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('thickness_gauge') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('thickness_gauge')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="thickness_mm" :value="__('Thickness (mm)')" />
                    <x-text-input id="thickness_mm" name="thickness_mm" type="number" step="0.0001" class="mt-1 block w-full" x-model="thicknessMm" x-on:input="updatePreview()" :value="old('thickness_mm')" placeholder="e.g. 0.64" />
                    <x-input-error :messages="$errors->get('thickness_mm')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="dimension" :value="__('Dimension (W x L) in Feet')" />
                    <x-text-input id="dimension" name="dimension" type="text" class="mt-1 block w-full" x-model="dimension" x-on:input="updatePreview()" :value="old('dimension')" placeholder="e.g. 4x10" />
                    <x-input-error :messages="$errors->get('dimension')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="diameter" :value="__('Diameter (inches)')" />
                    <x-text-input id="diameter" name="diameter" type="text" class="mt-1 block w-full" :value="old('diameter')" placeholder="e.g. 2.5" />
                    <x-input-error :messages="$errors->get('diameter')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select id="category_id" name="category_id" x-model="categoryId" @change="loadCustomFields()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Category</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="inventory_type" :value="__('Inventory Type')" />
                    <select id="inventory_type" name="inventory_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select type</option>
                        @foreach($dropdownOptions['inventory_types'] ?? [] as $opt)
                            <option value="{{ $opt->value }}" {{ old('inventory_type') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('inventory_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="folder_id" :value="__('Folder (Optional)')" />
                    <select id="folder_id" name="folder_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">No folder (Uncategorized)</option>
                        @foreach($folders ?? [] as $folder)
                            <option value="{{ $folder->id }}" {{ old('folder_id') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('folder_id')" class="mt-2" />
                </div>

                {{-- Row 4: Prices --}}
                <div>
                    <x-input-label for="unit_price" :value="__('Unit Purchase Price ($)')" />
                    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price')" required placeholder="0.00" />
                    <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="unit_sale_price" :value="__('Unit Sale Price ($)')" />
                    <x-text-input id="unit_sale_price" name="unit_sale_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_sale_price')" placeholder="0.00" />
                    <x-input-error :messages="$errors->get('unit_sale_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description / Notes')" />
                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" placeholder="Additional notes" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            {{-- Custom Fields Section --}}
            <div x-show="customFields.length > 0" x-transition class="p-4 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Additional Fields for this Category</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <template x-for="field in customFields" :key="field.id">
                        <div>
                            <label :for="'custom_field_' + field.id" class="block text-sm font-medium text-gray-700 dark:text-gray-300" x-text="field.field_label + (field.is_required ? ' *' : '')"></label>

                            <template x-if="field.field_type === 'text'">
                                <input type="text" :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            </template>

                            <template x-if="field.field_type === 'number'">
                                <input type="number" step="0.01" :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            </template>

                            <template x-if="field.field_type === 'select'">
                                <select :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select...</option>
                                    <template x-for="option in field.options" :key="option">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.field_type === 'textarea'">
                                <textarea :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Required Materials --}}
            <div>
                <x-input-label :value="__('Required Materials')" />
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Select the inventory items needed to produce this product, and how many units of each are consumed per unit built.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                    @foreach($inventoryItems ?? [] as $item)
                        <label x-data="{ checked: {{ in_array($item->id, old('material_ids', [])) ? 'true' : 'false' }} }" class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="checkbox" name="material_ids[]" value="{{ $item->id }}" x-model="checked" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                            <span class="flex-1 min-w-0 text-sm text-gray-700 dark:text-gray-300 truncate">{{ $item->name }}</span>
                            <input type="number" name="material_quantities[{{ $item->id }}]" min="0.01" max="999999" step="0.01" value="{{ old('material_quantities.' . $item->id, 1) }}" :disabled="!checked" :required="checked" title="Quantity required per unit built" class="w-20 px-2 py-1 text-xs border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-blue-500 focus:border-transparent disabled:opacity-40 disabled:cursor-not-allowed" />
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('material_ids')" class="mt-2" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Add Product
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
    function createProductForm() {
        const materialAbbr = {
            'Stainless Steel': 'SS', 'Aluminum': 'AL', 'Galvanize Steel': 'GS',
            'Black Iron': 'BI', 'Silicon': 'SI', 'Fire Wrap': 'FW', 'Glue': 'GL',
            'Paint': 'PT', 'Nozzle': 'NZ', 'Wire': 'WR', 'Electrode': 'EL',
            'Welding Stick': 'WS', 'Oxygen': 'O2', 'Nitrogen': 'N2', 'Propane': 'PR'
        };
        const itemTypeAbbr = {
            'Sheet Metal': 'S', 'Metal Accessory': 'MA', 'Miscellaneous': 'MISC',
            'Restaurant Fan': 'FAN', 'Food Truck Fan': 'FANFT', 'Angle Bar': 'AB',
            'Steel Tube': 'ST', 'Gas Valve': 'GV', 'Water Valve': 'WV',
            'Burner': 'BU', 'Pipe Fitter': 'PF', 'CNC Laser Cut': 'CNC'
        };

        return {
            itemType: '{{ old('item_type', '') }}',
            materialType: '{{ old('material_type', '') }}',
            materialGrade: '{{ old('material_grade', '') }}',
            thicknessGauge: '{{ old('thickness_gauge', '') }}',
            thicknessMm: '{{ old('thickness_mm', '') }}',
            dimension: '{{ old('dimension', '') }}',
            categoryId: '{{ old('category_id', '') }}',
            generatedLabel: '',
            generatedDescription: '',
            customFields: [],

            init() {
                this.updatePreview();
                if (this.categoryId) {
                    this.loadCustomFields();
                }
            },

            async loadCustomFields() {
                if (!this.categoryId) {
                    this.customFields = [];
                    return;
                }

                try {
                    const response = await fetch(`{{ route('products.custom-fields.get') }}?category_id=${this.categoryId}&item_type=${encodeURIComponent(this.itemType)}`);
                    const data = await response.json();
                    this.customFields = data.fields || [];
                } catch (error) {
                    console.error('Error loading custom fields:', error);
                    this.customFields = [];
                }
            },

            updatePreview() {
                let label = '';
                label += materialAbbr[this.materialType] || this.materialType.replace(/[^A-Za-z]/g, '').substring(0, 4).toUpperCase();
                label += itemTypeAbbr[this.itemType] || this.itemType.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
                if (this.materialGrade) label += 'GR' + this.materialGrade;
                if (this.thicknessGauge) label += 'GA' + this.thicknessGauge;
                else if (this.thicknessMm) label += 'MM' + this.thicknessMm;
                if (this.dimension) {
                    const parts = this.dimension.split(/[xX×]/);
                    if (parts.length === 2) label += 'W' + parts[0].trim() + 'L' + parts[1].trim();
                }
                this.generatedLabel = (this.materialType || this.itemType) ? label : '';

                let desc = [];
                if (this.materialType) desc.push(this.materialType);
                if (this.itemType) desc.push(this.itemType);
                if (this.materialGrade) desc.push('Grade ' + this.materialGrade);
                if (this.thicknessGauge) desc.push('Gauge ' + this.thicknessGauge);
                if (this.thicknessMm) desc.push('(' + this.thicknessMm + ' mm)');
                if (this.dimension) {
                    const parts = this.dimension.split(/[xX×]/);
                    if (parts.length === 2) desc.push('Width ' + parts[0].trim() + ' ft Length ' + parts[1].trim() + ' ft');
                    else desc.push(this.dimension);
                }
                this.generatedDescription = desc.join(' ');

                this.loadCustomFields();
            }
        };
    }
    </script>
</x-app-layout>
