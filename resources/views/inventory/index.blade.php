<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Raw Materials and Finished Goods</p>
                </div>
                <button x-data @click="$dispatch('open-modal', 'add-material')" class="inline-flex items-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add material
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Inventory Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Item #</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Item Label</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Inventory Type</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Diameter (inches)</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Item Type</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Material</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Gauge</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Thickness (mm)</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Dimension</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Reserve</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Last Added</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Purchase Price</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Sale Price</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Storage Location</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse (($items ?? collect()) as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->id }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm font-mono font-bold text-blue-700 dark:text-blue-400">{{ $item->item_label ?? $item->sku }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($item->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->sku }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->inventory_type ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->material_type ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->material_grade ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->thickness_gauge ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->thickness_mm ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->dimension ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($item->stock_quantity, 0) }} {{ $item->unit_of_measure ?? 'pcs' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($item->reserved_quantity ?? 0, 0) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    @if($item->last_added_quantity)
                                        +{{ $item->last_added_quantity }} (#{{ $item->id }} / {{ $item->updated_at ? $item->updated_at->format('d.m.Y') : '—' }})
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->unit_sale_price ? '$' . number_format($item->unit_sale_price, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->storageLocation->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($item->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">In Stock</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->vendor->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('inventory.show', $item) }}" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-blue-900/30 rounded-lg transition-all" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <x-confirm-delete
                                            :action="route('inventory.destroy', $item)"
                                            message="Are you sure you want to delete this inventory item? All transaction history will be lost."
                                            title="Delete Inventory Item"
                                            buttonClass="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </x-confirm-delete>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No inventory items found. <button x-data @click="$dispatch('open-modal', 'add-material')" class="text-blue-600 dark:text-blue-400 hover:underline">Add your first material</button>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if(isset($items) && method_exists($items, 'links'))
            <div class="mt-6">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Add Material Modal --}}
    <x-modal name="add-material" :show="false" maxWidth="4xl">
        <div class="p-6" x-data="inventoryForm()">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Add Material</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add a new item to inventory</p>

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

            <form method="POST" action="{{ route('inventory.store') }}" class="space-y-4">
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
                    {{-- Row 1: Item Type Label, Material Type, Material Grade --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item Type <span class="text-red-500">*</span></label>
                        <select name="item_type_label" x-model="itemTypeLabel" @change="updatePreview()" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
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

                    {{-- Row 3: Inventory Type, System Item Type, Storage Location --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inventory Type</label>
                        <select name="inventory_type" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select type</option>
                            @foreach($dropdownOptions['inventory_types'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                     {{-- Row 3: Diameter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Diameter (inches)
                        </label>
                        <input type="text" name="diameter" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="e.g. 2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">System Category <span class="text-red-500">*</span></label>
                        <select name="item_type" x-model="itemType" @change="loadCustomFields()" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select category</option>
                            @foreach($dropdownOptions['system_categories'] as $opt)
                                <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Storage Location</label>
                        <select name="storage_location_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select location</option>
                            @foreach($storageLocations ?? [] as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 4: Stock, Reserve, Last Added --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reserve</label>
                        <input type="number" name="reserved_quantity" value="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Added Quantity</label>
                        <input type="number" name="last_added_quantity" value="" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0">
                    </div>

                    {{-- Row 5: Prices, UoM --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Purchase Price ($)</label>
                        <input type="number" step="0.01" name="unit_price" value="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Sale Price ($)</label>
                        <input type="number" step="0.01" name="unit_sale_price" value="" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit of Measure</label>
                        <select name="unit_of_measure" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="m">Meters (m)</option>
                            <option value="m2">Square Meters (m2)</option>
                            <option value="l">Liters (l)</option>
                            <option value="sheets">Sheets</option>
                            <option value="rolls">Rolls</option>
                            <option value="box">Box</option>
                        </select>
                    </div>

                    {{-- Row 6: Vendor, Min Level, Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor</label>
                        <select name="vendor_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select vendor</option>
                            @foreach($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min. Stock Level</label>
                        <input type="number" name="minimum_stock_level" value="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <input type="text" name="description" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Additional notes">
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

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg transition-all">
                        Add material
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function inventoryForm() {
        const materialAbbr = {
            'Stainless Steel': 'SS', 'Aluminum': 'AL', 'Galvanize Steel': 'GS',
            'Black Iron': 'BI', 'Silicon': 'SI', 'Fire Wrap': 'FW', 'Glue': 'GL'
        };
        const itemTypeAbbr = {
            'Sheet Metal': 'S', 'Metal Accessory': 'MA', 'Miscellaneous': 'MISC',
            'Restaurant Fan': 'FAN', 'Food Truck Fan': 'FANFT'
        };

        return {
            itemTypeLabel: '', materialType: '', materialGrade: '',
            thicknessGauge: '', thicknessMm: '', dimension: '',
            generatedLabel: '', generatedDescription: '',
            itemType: '', customFields: [],

            async loadCustomFields() {
                if (!this.itemType) {
                    this.customFields = [];
                    return;
                }
                try {
                    const response = await fetch(`{{ route('inventory.custom-fields.get') }}?item_type=${encodeURIComponent(this.itemType)}`);
                    const data = await response.json();
                    this.customFields = data.fields || [];
                } catch (e) {
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
