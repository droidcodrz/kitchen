        <div class="flex gap-6">
            <!-- Folder Sidebar -->
            <div class="w-64 flex-shrink-0">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Folders</h3>
                        <button x-data @click="$dispatch('open-modal', 'create-folder')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Create Folder">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <nav class="space-y-1">
                        <!-- All Products -->
                        <a href="{{ route('products.index') }}"
                           wire:navigate
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all
                                  {{ !request()->has('folder') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                All Products
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $totalProductsCount ?? 0 }}</span>
                        </a>

                        <!-- Uncategorized -->
                        <a href="{{ route('products.index', ['folder' => 'none']) }}"
                           wire:navigate
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all
                                  {{ request()->get('folder') === 'none' ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                Uncategorized
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $uncategorizedProductsCount ?? 0 }}</span>
                        </a>

                        @foreach($folders ?? [] as $folder)
                            <div x-data="{ open: false }"
                                 class="relative flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all
                                        {{ request()->get('folder') == $folder->id ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('products.index', ['folder' => $folder->id]) }}"
                                   wire:navigate
                                   class="flex items-center flex-1 min-w-0">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span class="truncate">{{ $folder->name }}</span>
                                </a>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $folder->products_count }}</span>
                                    <button type="button" @click.stop.prevent="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <button type="button" @click="open = false; $dispatch('open-modal', 'edit-folder-{{ $folder->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-t-lg">
                                        Rename
                                    </button>
                                    <button type="button" @click="open = false; $dispatch('open-modal', 'delete-folder-{{ $folder->id }}')" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-b-lg">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Edit Folder Modal -->
                            <x-modal name="edit-folder-{{ $folder->id }}" :show="false" maxWidth="md">
                                <div class="p-6">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rename Folder</h2>
                                    <form method="POST" action="{{ route('product-folders.update', $folder) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Folder Name</label>
                                            <input type="text" name="name" value="{{ $folder->name }}" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                            <textarea name="description" rows="2" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">{{ $folder->description }}</textarea>
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">Cancel</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>

                            <!-- Delete Folder Modal -->
                            <x-modal name="delete-folder-{{ $folder->id }}" :show="false" maxWidth="md">
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Folder</h2>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $folder->name }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Are you sure you want to delete this folder? Products will be moved to <strong>Uncategorized</strong>.
                                        </p>
                                        @if($folder->products_count > 0)
                                            <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                    <strong>{{ $folder->products_count }}</strong> product(s) will be moved to Uncategorized.
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('product-folders.destroy', $folder) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="flex justify-end gap-3">
                                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-colors">
                                                Delete Folder
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Products Content -->
            <div class="flex-1 min-w-0">
        @if(($view ?? 'grid') === 'table')
            {{-- Table View --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Label</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Material Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gauge</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dimension</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Purchase Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sale Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse (($products ?? collect()) as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $product->id }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm font-mono font-bold text-blue-700 dark:text-blue-400">{{ $product->item_label ?? $product->sku }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->item_type ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->material_type ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->material_grade ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->thickness_gauge ? $product->thickness_gauge : ($product->thickness_mm ? $product->thickness_mm . ' mm' : '—') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->dimension ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($product->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $product->unit_sale_price ? '$' . number_format($product->unit_sale_price, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($product->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <div x-data="moveProductDropdown()" class="relative">
                                                <button @click="open = !open" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" title="Move to Folder">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 max-h-48 overflow-y-auto">
                                                    <div class="py-1">
                                                        <button type="button" :disabled="moving" @click="move({{ $product->id }}, '')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Uncategorized
                                                        </button>
                                                        @foreach($folders ?? [] as $folder)
                                                            <button type="button" :disabled="moving" @click="move({{ $product->id }}, '{{ $folder->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                {{ $folder->name }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('products.edit', $product) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <x-confirm-delete
                                                :action="route('products.destroy', $product)"
                                                message="Are you sure you want to delete this product? This action cannot be undone."
                                                title="Delete Product"
                                                buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </x-confirm-delete>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new product.</p>
                                        <div class="mt-6">
                                            <button x-data @click="$dispatch('open-modal', 'add-product')" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Add Product
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- Grid View --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse (($products ?? collect()) as $product)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                    {{-- Item Label --}}
                    <div class="mb-3">
                        <p class="text-xs font-mono font-bold text-blue-700 dark:text-blue-400 mb-1">{{ $product->item_label ?? $product->sku }}</p>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                            {{ $product->name }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </p>
                    </div>

                    {{-- Specs --}}
                    <div class="mb-3 space-y-1">
                        @if($product->material_type)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Material</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $product->material_type }}</span>
                            </div>
                        @endif
                        @if($product->material_grade)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Grade</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $product->material_grade }}</span>
                            </div>
                        @endif
                        @if($product->thickness_gauge)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Gauge</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $product->thickness_gauge }}</span>
                            </div>
                        @endif
                        @if($product->dimension)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Dimension</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $product->dimension }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-2 mb-3">
                        @if($product->item_type)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $product->item_type }}
                            </span>
                        @endif
                        @if($product->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Purchase Price</span>
                            <span class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($product->unit_price, 2) }}</span>
                        </div>
                        @if($product->unit_sale_price)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Sale Price</span>
                            <span class="text-sm font-medium text-green-700 dark:text-green-400">${{ number_format($product->unit_sale_price, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200 dark:border-gray-800">
                        <div x-data="moveProductDropdown()" class="relative">
                            <button @click="open = !open" class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex items-center" title="Move to Folder">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                Move
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 bottom-full mb-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 max-h-48 overflow-y-auto">
                                <div class="py-1">
                                    <button type="button" :disabled="moving" @click="move({{ $product->id }}, '')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Uncategorized
                                    </button>
                                    @foreach($folders ?? [] as $folder)
                                        <button type="button" :disabled="moving" @click="move({{ $product->id }}, '{{ $folder->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            {{ $folder->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="icon-btn" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <x-confirm-delete
                                :action="route('products.destroy', $product)"
                                message="Are you sure you want to delete this product? This action cannot be undone."
                                title="Delete Product"
                                buttonClass="icon-btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </x-confirm-delete>
                        </div>
                    </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new product.</p>
                            <div class="mt-6">
                                <button x-data @click="$dispatch('open-modal', 'add-product')" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Product
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

                {{-- Pagination --}}
                @if(isset($products) && method_exists($products, 'links'))
                    <div class="mt-6">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        <script>
        function moveProductDropdown() {
            return {
                open: false,
                moving: false,

                async move(productId, folderId) {
                    this.moving = true;
                    this.open = false;

                    try {
                        const response = await fetch('{{ route('product-folders.move-product') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                            body: JSON.stringify({ product_id: productId, folder_id: folderId || null }),
                        });

                        if (!response.ok) {
                            this.moving = false;
                            return;
                        }

                        const listResponse = await fetch(window.location.href, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const html = await listResponse.text();
                        document.getElementById('products-main').innerHTML = html;
                    } catch (e) {
                        this.moving = false;
                    }
                },
            };
        }
        </script>
