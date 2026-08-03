<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        <span class="font-mono text-blue-700 dark:text-blue-400">{{ $inventoryItem->item_label ?? $inventoryItem->sku }}</span>
                        <span class="text-gray-400 mx-1">—</span>
                        {{ $inventoryItem->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Item #{{ $inventoryItem->id }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <x-status-badge :status="$inventoryItem->status" />
                @if($inventoryItem->is_low_stock)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                        Low Stock
                    </span>
                @endif
                <a href="{{ route('inventory.edit', $inventoryItem) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Material Details</h3>

                @if($inventoryItem->item_label)
                    <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Item Label</p>
                        <p class="text-lg font-bold text-blue-700 dark:text-blue-400 font-mono">{{ $inventoryItem->item_label }}</p>
                    </div>
                @endif

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @if($inventoryItem->material_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Material Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->material_type }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Item Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $inventoryItem->item_type)) }}</dd>
                    </div>
                    @if($inventoryItem->material_grade)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Material Grade</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->material_grade }}</dd>
                    </div>
                    @endif
                    @if($inventoryItem->thickness_gauge)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Thickness (Gauge)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->thickness_gauge }}</dd>
                    </div>
                    @endif
                    @if($inventoryItem->thickness_mm)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Thickness (mm)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->thickness_mm }}</dd>
                    </div>
                    @endif
                    @if($inventoryItem->dimension)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dimension (W x L)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->dimension }}</dd>
                    </div>
                    @endif
                    @if($inventoryItem->inventory_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Inventory Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->inventory_type }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Price</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($inventoryItem->unit_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sale Price</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->unit_sale_price ? '$' . number_format($inventoryItem->unit_sale_price, 2) : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vendor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->vendor->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Location</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->storageLocation->name ?? 'N/A' }}</dd>
                    </div>
                    @if($inventoryItem->unit_of_measure)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit of Measure</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->unit_of_measure }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($inventoryItem->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inventoryItem->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Stock Info --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6" x-data>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Stock Information</h3>
                    <button type="button" @click="$dispatch('open-modal', 'adjust-stock')" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Adjust Stock
                    </button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($inventoryItem->stock_quantity, 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Stock</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($inventoryItem->reserved_quantity, 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reserved</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold {{ $inventoryItem->available_quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($inventoryItem->available_quantity, 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($inventoryItem->minimum_stock_level, 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Min. Level</p>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Performed By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($inventoryItem->inventoryTransactions()->latest()->take(10)->get() as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    @php
                                        $isPositive = in_array($transaction->type, ['addition', 'release']);
                                    @endphp
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isPositive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-medium {{ $isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $isPositive ? '+' : '-' }}{{ number_format($transaction->quantity, 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->performer->full_name ?? 'System' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No transactions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Used in Products --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Used in Products</h3>
                @if($inventoryItem->products->count() > 0)
                    <div class="space-y-2">
                        @foreach($inventoryItem->products as $product)
                            <a href="{{ route('products.show', $product) }}" class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600/50 transition">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">Not used in any products.</p>
                @endif
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6 border border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
                <x-confirm-delete :action="route('inventory.destroy', $inventoryItem)" message="Are you sure you want to delete this inventory item? All transaction history will be lost.">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Item
                </x-confirm-delete>
            </div>
        </div>
    </div>

    {{-- Adjust Stock Modal --}}
    <x-modal name="adjust-stock" :show="$errors->hasAny(['type', 'quantity', 'notes'])" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Adjust Stock</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Current stock: {{ number_format($inventoryItem->stock_quantity, 0) }} {{ $inventoryItem->unit_of_measure }}</p>

            <form method="POST" action="{{ route('inventory.adjust-stock', $inventoryItem) }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="addition" {{ old('type') === 'addition' ? 'selected' : '' }}>Addition (restock)</option>
                        <option value="deduction" {{ old('type') === 'deduction' ? 'selected' : '' }}>Deduction (used/damaged/lost)</option>
                        <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Adjustment (set exact quantity)</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="quantity" :value="__('Quantity')" />
                    <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('quantity')" required />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="notes" :value="__('Notes (optional)')" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
