<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage product details, pricing, and categories</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- View Toggle -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                        <a href="{{ route('products.index', ['view' => 'grid']) }}"
                           wire:navigate
                           class="p-2 rounded {{ ($view ?? 'grid') === 'grid' ? 'bg-white dark:bg-gray-700 shadow-sm' : '' }}"
                           title="Grid View">
                            <svg class="w-5 h-5 {{ ($view ?? 'grid') === 'grid' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </a>
                        <a href="{{ route('products.index', ['view' => 'table']) }}"
                           wire:navigate
                           class="p-2 rounded {{ ($view ?? 'grid') === 'table' ? 'bg-white dark:bg-gray-700 shadow-sm' : '' }}"
                           title="Table View">
                            <svg class="w-5 h-5 {{ ($view ?? 'grid') === 'table' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </a>
                    </div>
                    <button x-data @click="$dispatch('open-modal', 'add-product')" class="inline-flex items-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="products-main">
            @include('products._main')
        </div>
    </div>

    {{-- Add Product Modal --}}
    <x-modal name="add-product" :show="false" maxWidth="4xl">
        <div class="p-6" x-data="productForm()">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">New Product</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add a new equipment item to the inventory</p>

            {{-- Live Preview --}}
            <div class="mb-5 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
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

            <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Name (optional)</label>
                        <input type="text" name="name" placeholder="Leave blank to use the auto-generated description" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name Suffix (optional)</label>
                        <input type="text" name="name_suffix" placeholder="e.g. Type 1, Version A, Brass Handle" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Row 1: Item Type, Material Type, Material Grade --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item Type <span class="text-red-500">*</span></label>
                        <select name="item_type" x-model="itemType" @change="updatePreview(); loadCustomFields();" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select item type</option>
                            @foreach($dropdownOptions['item_types'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Material Type <span class="text-red-500">*</span></label>
                        <select name="material_type" x-model="materialType" @change="updatePreview()" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select material type</option>
                            @foreach($dropdownOptions['material_types'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Material Grade</label>
                        <select name="material_grade" x-model="materialGrade" @change="updatePreview()" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">No grade</option>
                            @foreach($dropdownOptions['material_grades'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 2: Gauge, Thickness mm, Dimension --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thickness (Gauge)</label>
                        <select name="thickness_gauge" x-model="thicknessGauge" @change="updatePreview()" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">No gauge</option>
                            @foreach($dropdownOptions['thickness_gauges'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thickness (mm)</label>
                        <input type="number" step="0.0001" name="thickness_mm" x-model="thicknessMm" @input="updatePreview()" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="e.g. 0.64">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dimension (W x L) in Feet</label>
                        <input type="text" name="dimension" x-model="dimension" @input="updatePreview()" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="e.g. 4x10">
                    </div>

                    {{-- Row 3: Diameter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Diameter (inches)
                        </label>
                        <input type="text" name="diameter" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="e.g. 2.5">
                    </div>

                    {{-- Row 3 continued: Category, Inventory Type, Folder --}}
                    <div x-data="{ showQuickAdd: false, newCategoryName: '' }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="category_id" id="modal-category-select" x-model="categoryId" @change="loadCustomFields()" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">Select category</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" @click="showQuickAdd = !showQuickAdd" class="flex-shrink-0 px-2.5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all" title="Add new category">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <div x-show="showQuickAdd" x-cloak class="mt-2 flex gap-2">
                            <input type="text" x-model="newCategoryName" placeholder="New category name" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="button" @click="
                                if (!newCategoryName.trim()) return;
                                fetch('{{ route('categories.quick-add') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                                    body: JSON.stringify({ name: newCategoryName })
                                })
                                .then(r => r.json())
                                .then(data => {
                                    const select = document.getElementById('modal-category-select');
                                    const option = new Option(data.name, data.id, true, true);
                                    select.add(option);
                                    newCategoryName = '';
                                    showQuickAdd = false;
                                })
                            " class="flex-shrink-0 px-3 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-all whitespace-nowrap">
                                Save
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inventory Type</label>
                        <select name="inventory_type" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select type</option>
                            <option value="Raw Materials">Raw Materials</option>
                            <option value="Finished Goods">Finished Goods</option>
                            <option value="Merchandise Goods">Merchandise Goods</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Folder (Optional)</label>
                        <select name="folder_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">No folder</option>
                            @foreach($folders ?? [] as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 4: Prices --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Purchase Price ($) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="unit_price" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Sale Price ($)</label>
                        <input type="number" step="0.01" name="unit_sale_price" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                        <input type="text" name="description" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Additional notes">
                    </div>
                </div>

                {{-- Required Materials --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Required Materials</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Select the inventory items needed to produce this product, and how many units of each are consumed per unit built.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        @foreach($inventoryItems ?? [] as $item)
                            <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                <input type="checkbox" name="material_ids[]" value="{{ $item->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <span class="flex-1 min-w-0 text-sm text-gray-700 dark:text-gray-300 truncate">{{ $item->name }}</span>
                                <input type="number" name="material_quantities[{{ $item->id }}]" min="0.01" step="0.01" value="1" title="Quantity required per unit built" class="w-20 px-2 py-1 text-xs border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-blue-500 focus:border-transparent" />
                            </label>
                        @endforeach
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

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg transition-all">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function productForm() {
        const materialAbbr = {
            'Stainless Steel': 'SS', 'Aluminum': 'AL', 'Galvanize Steel': 'GS',
            'Black Iron': 'BI', 'Silicon': 'SI', 'Fire Wrap': 'FW', 'Glue': 'GL'
        };
        const itemTypeAbbr = {
            'Sheet Metal': 'S', 'Metal Accessory': 'MA', 'Miscellaneous': 'MISC',
            'Restaurant Fan': 'FAN', 'Food Truck Fan': 'FANFT'
        };

        return {
            itemType: '', materialType: '', materialGrade: '',
            thicknessGauge: '', thicknessMm: '', dimension: '',
            categoryId: '',
            generatedLabel: '', generatedDescription: '',
            customFields: [],

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
            }
        };
    }
    </script>

    {{-- Create Folder Modal --}}
    <x-modal name="create-folder" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create Folder</h2>
            <form method="POST" action="{{ route('product-folders.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Folder Name</label>
                    <input type="text" name="name" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="2" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm" placeholder="Optional"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Create Folder</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
