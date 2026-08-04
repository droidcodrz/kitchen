<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add Material') }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg" x-data="createInventoryForm()">
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

        <form method="POST" action="{{ route('inventory.store') }}" class="p-6 space-y-6">
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
                {{-- Row 1: Item Type Label, Material Type, Material Grade --}}
                <div>
                    <x-input-label for="item_type_label" :value="__('Item Type')" />
                    <select id="item_type_label" name="item_type_label" x-model="itemTypeLabel" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select item type</option>
                        @foreach($dropdownOptions['item_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('item_type_label') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('item_type_label')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="material_type" :value="__('Material Type')" />
                    <select id="material_type" name="material_type" x-model="materialType" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
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

                {{-- Row 3: Diameter, Inventory Type, System Category --}}
                <div>
                    <x-input-label for="diameter" :value="__('Diameter (inches)')" />
                    <x-text-input id="diameter" name="diameter" type="text" class="mt-1 block w-full" :value="old('diameter')" placeholder="e.g. 2.5" />
                    <x-input-error :messages="$errors->get('diameter')" class="mt-2" />
                </div>

                {{-- Row 3 continued: Inventory Type, System Category, Storage Location --}}
                <div>
                    <x-input-label for="inventory_type" :value="__('Inventory Type')" />
                    <select id="inventory_type" name="inventory_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select type</option>
                        @foreach($dropdownOptions['inventory_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('inventory_type') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('inventory_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="item_type" :value="__('System Category')" />
                    <select id="item_type" name="item_type" x-model="itemType" @change="loadCustomFields()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select category</option>
                        @foreach($dropdownOptions['system_categories'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('item_type') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('item_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="storage_location_id" :value="__('Storage Location')" />
                    <select id="storage_location_id" name="storage_location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Location</option>
                        @foreach($storageLocations ?? [] as $location)
                            <option value="{{ $location->id }}" {{ old('storage_location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->code }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('storage_location_id')" class="mt-2" />
                </div>

                {{-- Row 4: Stock, Prices, Vendor --}}
                {{-- Row 4: Stock, Reserve, Last Added --}}
                <div>
                    <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                    <x-text-input id="stock_quantity" name="stock_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('stock_quantity', 0)" />
                    <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="reserved_quantity" :value="__('Reserved Quantity')" />
                    <x-text-input id="reserved_quantity" name="reserved_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('reserved_quantity', 0)" />
                    <x-input-error :messages="$errors->get('reserved_quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="last_added_quantity" :value="__('Last Added Quantity')" />
                    <x-text-input id="last_added_quantity" name="last_added_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('last_added_quantity')" placeholder="0" />
                    <x-input-error :messages="$errors->get('last_added_quantity')" class="mt-2" />
                </div>

                {{-- Row 5: Prices, UoM --}}
                <div>
                    <x-input-label for="unit_price" :value="__('Unit Purchase Price ($)')" />
                    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price')" placeholder="0.00" />
                    <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="unit_sale_price" :value="__('Unit Sale Price ($)')" />
                    <x-text-input id="unit_sale_price" name="unit_sale_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_sale_price')" placeholder="0.00" />
                    <x-input-error :messages="$errors->get('unit_sale_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="unit_of_measure" :value="__('Unit of Measure')" />
                    <select id="unit_of_measure" name="unit_of_measure" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach(['pcs' => 'Pieces (pcs)', 'kg' => 'Kilograms (kg)', 'm' => 'Meters (m)', 'm2' => 'Square Meters (m²)', 'l' => 'Liters (l)', 'sheets' => 'Sheets', 'rolls' => 'Rolls', 'box' => 'Box'] as $val => $label)
                            <option value="{{ $val }}" {{ old('unit_of_measure', 'pcs') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('unit_of_measure')" class="mt-2" />
                </div>

                {{-- Row 6: Min Level, Vendor, Description --}}
                <div>
                    <x-input-label for="minimum_stock_level" :value="__('Minimum Stock Level')" />
                    <x-text-input id="minimum_stock_level" name="minimum_stock_level" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('minimum_stock_level', 0)" />
                    <x-input-error :messages="$errors->get('minimum_stock_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="vendor_id" :value="__('Vendor')" />
                    <select id="vendor_id" name="vendor_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Vendor</option>
                        @foreach($vendors ?? [] as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description / Notes')" />
                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" placeholder="Additional notes" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            {{-- Custom Fields Section --}}
            <div x-show="customFields.length > 0" x-transition class="p-4 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Additional Fields</h3>
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

                            <template x-if="field.field_type === 'date'">
                                <input type="date" :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            </template>

                            <template x-if="field.field_type === 'select'">
                                <select :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select...</option>
                                    <template x-for="option in field.options" :key="option">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.field_type === 'boolean'">
                                <select :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </template>

                            <template x-if="field.field_type === 'textarea'">
                                <textarea :name="'custom_fields[' + field.id + ']'" :id="'custom_field_' + field.id" :required="field.is_required" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Add Material
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
    function createInventoryForm() {
        const materialAbbr = {
            'Stainless Steel': 'SS', 'Aluminum': 'AL', 'Galvanize Steel': 'GS',
            'Black Iron': 'BI', 'Silicon': 'SI', 'Fire Wrap': 'FW', 'Glue': 'GL'
        };
        const itemTypeAbbr = {
            'Sheet Metal': 'S', 'Metal Accessory': 'MA', 'Miscellaneous': 'MISC',
            'Restaurant Fan': 'FAN', 'Food Truck Fan': 'FANFT'
        };

        return {
            itemTypeLabel: '{{ old('item_type_label', '') }}',
            itemType: '{{ old('item_type', '') }}',
            materialType: '{{ old('material_type', '') }}',
            materialGrade: '{{ old('material_grade', '') }}',
            thicknessGauge: '{{ old('thickness_gauge', '') }}',
            thicknessMm: '{{ old('thickness_mm', '') }}',
            dimension: '{{ old('dimension', '') }}',
            generatedLabel: '',
            generatedDescription: '',
            customFields: [],

            init() {
                this.updatePreview();
                if (this.itemType) {
                    this.loadCustomFields();
                }
            },

            async loadCustomFields() {
                if (!this.itemType) {
                    this.customFields = [];
                    return;
                }

                try {
                    const response = await fetch(`{{ route('inventory.custom-fields.get') }}?item_type=${encodeURIComponent(this.itemType)}`);
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
                label += itemTypeAbbr[this.itemTypeLabel] || this.itemTypeLabel.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
                if (this.materialGrade) label += 'GR' + this.materialGrade;
                if (this.thicknessGauge) label += 'GA' + this.thicknessGauge;
                else if (this.thicknessMm) label += 'MM' + this.thicknessMm;
                if (this.dimension) {
                    const parts = this.dimension.split(/[xX×]/);
                    if (parts.length === 2) label += 'W' + parts[0].trim() + 'L' + parts[1].trim();
                }
                this.generatedLabel = (this.materialType || this.itemTypeLabel) ? label : '';

                let desc = [];
                if (this.materialType) desc.push(this.materialType);
                if (this.itemTypeLabel) desc.push(this.itemTypeLabel);
                if (this.materialGrade) desc.push('Grade ' + this.materialGrade);
                if (this.thicknessGauge) desc.push('Gauge ' + this.thicknessGauge);
                if (this.thicknessMm) desc.push('(' + this.thicknessMm + ' mm)');
                if (this.dimension) {
                    const parts = this.dimension.split(/[xX×]/);
                    if (parts.length === 2) desc.push('Width ' + parts[0].trim() + ' ft Length ' + parts[1].trim() + ' ft');
                    else desc.push(this.dimension);
                }
                this.generatedDescription = desc.join(' ');
            }
        };
    }
    </script>
</x-app-layout>
