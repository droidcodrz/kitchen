<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('inventory.show', $inventoryItem) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Material') }}: {{ $inventoryItem->item_label ?? $inventoryItem->name }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg" x-data="editInventoryForm()">
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

        <form method="POST" action="{{ route('inventory.update', $inventoryItem) }}" class="p-6 space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Row 1: Item Type Label, Material Type, Material Grade --}}
                <div>
                    <x-input-label for="item_type_label" :value="__('Item Type')" />
                    <select id="item_type_label" name="item_type_label" x-model="itemTypeLabel" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select item type</option>
                        @foreach($dropdownOptions['item_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('item_type_label', $inventoryItem->item_type_label ?? '') === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="material_type" :value="__('Material Type')" />
                    <select id="material_type" name="material_type" x-model="materialType" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select material type</option>
                        @foreach($dropdownOptions['material_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('material_type', $inventoryItem->material_type) === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('material_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="material_grade" :value="__('Material Grade')" />
                    <select id="material_grade" name="material_grade" x-model="materialGrade" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">No grade</option>
                        @foreach($dropdownOptions['material_grades'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('material_grade', $inventoryItem->material_grade) === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Row 2: Gauge, Thickness mm, Dimension --}}
                <div>
                    <x-input-label for="thickness_gauge" :value="__('Thickness (Gauge)')" />
                    <select id="thickness_gauge" name="thickness_gauge" x-model="thicknessGauge" @change="updatePreview()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">No gauge</option>
                        @foreach($dropdownOptions['thickness_gauges'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('thickness_gauge', $inventoryItem->thickness_gauge) === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="thickness_mm" :value="__('Thickness (mm)')" />
                    <x-text-input id="thickness_mm" name="thickness_mm" type="number" step="0.0001" class="mt-1 block w-full" x-model="thicknessMm" x-on:input="updatePreview()" :value="old('thickness_mm', $inventoryItem->thickness_mm)" />
                </div>

                <div>
                    <x-input-label for="dimension" :value="__('Dimension (W x L) in Feet')" />
                    <x-text-input id="dimension" name="dimension" type="text" class="mt-1 block w-full" x-model="dimension" x-on:input="updatePreview()" :value="old('dimension', $inventoryItem->dimension)" placeholder="e.g. 4x10" />
                </div>

                {{-- Row 3: Diameter, Inventory Type, System Category --}}
                <div>
                    <x-input-label for="diameter" :value="__('Diameter (inches)')" />
                    <x-text-input id="diameter" name="diameter" type="text" class="mt-1 block w-full" :value="old('diameter', $inventoryItem->diameter)" placeholder="e.g. 2.5" />
                </div>

                {{-- Row 3 continued: Inventory Type, System Category, Storage Location --}}
                <div>
                    <x-input-label for="inventory_type" :value="__('Inventory Type')" />
                    <select id="inventory_type" name="inventory_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select type</option>
                        @foreach($dropdownOptions['inventory_types'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('inventory_type', $inventoryItem->inventory_type) === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="item_type" :value="__('System Category')" />
                    <select id="item_type" name="item_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Category</option>
                        @foreach($dropdownOptions['system_categories'] as $opt)
                            <option value="{{ $opt->value }}" {{ old('item_type', $inventoryItem->item_type) === $opt->value ? 'selected' : '' }}>{{ $opt->label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('item_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="storage_location_id" :value="__('Storage Location')" />
                    <select id="storage_location_id" name="storage_location_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Location</option>
                        @foreach($storageLocations ?? [] as $location)
                            <option value="{{ $location->id }}" {{ old('storage_location_id', $inventoryItem->storage_location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Row 4: Stock, Reserve, Last Added --}}
                <div>
                    <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                    <x-text-input id="stock_quantity" name="stock_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('stock_quantity', $inventoryItem->stock_quantity)" />
                </div>

                <div>
                    <x-input-label for="reserved_quantity" :value="__('Reserved Quantity')" />
                    <x-text-input id="reserved_quantity" name="reserved_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('reserved_quantity', $inventoryItem->reserved_quantity)" />
                </div>

                <div>
                    <x-input-label for="last_added_quantity" :value="__('Last Added Quantity')" />
                    <x-text-input id="last_added_quantity" name="last_added_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('last_added_quantity', $inventoryItem->last_added_quantity)" placeholder="0" />
                </div>

                {{-- Row 5: Prices, UoM --}}
                <div>
                    <x-input-label for="unit_price" :value="__('Unit Purchase Price ($)')" />
                    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price', $inventoryItem->unit_price)" />
                </div>

                <div>
                    <x-input-label for="unit_sale_price" :value="__('Unit Sale Price ($)')" />
                    <x-text-input id="unit_sale_price" name="unit_sale_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_sale_price', $inventoryItem->unit_sale_price)" />
                </div>

                <div>
                    <x-input-label for="unit_of_measure" :value="__('Unit of Measure')" />
                    <select id="unit_of_measure" name="unit_of_measure" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach(['pcs' => 'Pieces (pcs)', 'kg' => 'Kilograms (kg)', 'm' => 'Meters (m)', 'm2' => 'Square Meters (m²)', 'l' => 'Liters (l)', 'sheets' => 'Sheets', 'rolls' => 'Rolls', 'box' => 'Box'] as $val => $label)
                            <option value="{{ $val }}" {{ old('unit_of_measure', $inventoryItem->unit_of_measure ?? 'pcs') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Row 6: Min Level, Vendor, Status --}}
                <div>
                    <x-input-label for="minimum_stock_level" :value="__('Minimum Stock Level')" />
                    <x-text-input id="minimum_stock_level" name="minimum_stock_level" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('minimum_stock_level', $inventoryItem->minimum_stock_level)" />
                </div>

                <div>
                    <x-input-label for="vendor_id" :value="__('Vendor')" />
                    <select id="vendor_id" name="vendor_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Vendor</option>
                        @foreach($vendors ?? [] as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $inventoryItem->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="is_active" :value="__('Status')" />
                    <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1" {{ old('is_active', $inventoryItem->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $inventoryItem->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="description" :value="__('Description / Notes')" />
                    <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $inventoryItem->description) }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('inventory.show', $inventoryItem) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update Material
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
    function editInventoryForm() {
        const materialAbbr = {
            'Stainless Steel': 'SS', 'Aluminum': 'AL', 'Galvanize Steel': 'GS',
            'Black Iron': 'BI', 'Silicon': 'SI', 'Fire Wrap': 'FW', 'Glue': 'GL'
        };
        const itemTypeAbbr = {
            'Sheet Metal': 'S', 'Metal Accessory': 'MA', 'Miscellaneous': 'MISC',
            'Restaurant Fan': 'FAN', 'Food Truck Fan': 'FANFT'
        };

        return {
            itemTypeLabel: '{{ old('item_type_label', $inventoryItem->item_type_label ?? '') }}',
            materialType: '{{ old('material_type', $inventoryItem->material_type ?? '') }}',
            materialGrade: '{{ old('material_grade', $inventoryItem->material_grade ?? '') }}',
            thicknessGauge: '{{ old('thickness_gauge', $inventoryItem->thickness_gauge ?? '') }}',
            thicknessMm: '{{ old('thickness_mm', $inventoryItem->thickness_mm ?? '') }}',
            dimension: '{{ old('dimension', $inventoryItem->dimension ?? '') }}',
            generatedLabel: '',
            generatedDescription: '',

            init() { this.updatePreview(); },

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
