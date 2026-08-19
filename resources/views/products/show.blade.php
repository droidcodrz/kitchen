<x-app-layout>
    <x-slot name="title">Product Details</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <span class="font-mono text-blue-700 dark:text-blue-400">{{ $product->item_label ?? $product->sku }}</span>
                    <span class="text-gray-400 mx-1">—</span>
                    {{ $product->name }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                @php $activeProjects = $product->activeProjectsCount(); @endphp
                <x-confirm-delete :action="route('products.destroy', $product)"
                    :disabled="$activeProjects > 0"
                    disabledReason="In use by {{ $activeProjects }} active project(s) - cannot be deleted.">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </x-confirm-delete>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Product Details</h3>

                @if($product->item_label)
                    <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Item Label</p>
                        <p class="text-lg font-bold text-blue-700 dark:text-blue-400 font-mono">{{ $product->item_label }}</p>
                    </div>
                @endif

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Item #</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->category->name ?? 'Uncategorized' }}</dd>
                    </div>
                    @if($product->item_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Item Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->item_type }}</dd>
                    </div>
                    @endif
                    @if($product->material_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Material Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->material_type }}</dd>
                    </div>
                    @endif
                    @if($product->material_grade)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Material Grade</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->material_grade }}</dd>
                    </div>
                    @endif
                    @if($product->thickness_gauge)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Thickness (Gauge)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->thickness_gauge }}</dd>
                    </div>
                    @endif
                    @if($product->thickness_mm)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Thickness (mm)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->thickness_mm }}</dd>
                    </div>
                    @endif
                    @if($product->dimension)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dimension (W x L)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->dimension }}</dd>
                    </div>
                    @endif
                    @if($product->inventory_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Inventory Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->inventory_type }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Price</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($product->unit_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sale Price</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->unit_sale_price ? '$' . number_format($product->unit_sale_price, 2) : '—' }}</dd>
                    </div>
                    @if($product->diameter)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Diameter (inches)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->diameter }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1"><x-status-badge :status="$product->is_active ? 'active' : 'inactive'" /></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($product->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->description }}</dd>
                        </div>
                    @endif
                </dl>

                @if($product->customFieldValues->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Additional Fields</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach($product->customFieldValues as $fieldValue)
                                @if($fieldValue->definition)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $fieldValue->definition->field_label }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            @if($fieldValue->definition->field_type === 'boolean')
                                                {{ $fieldValue->value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $fieldValue->value ?: '—' }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>

            {{-- Projects using this product --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Used in Projects</h3>
                @if($product->projects->count() > 0)
                    <div class="space-y-3">
                        @foreach($product->projects as $project)
                            <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600/50 transition">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->name ?? 'N/A' }} - Qty: {{ $project->pivot->quantity }}</p>
                                </div>
                                <x-status-badge :status="$project->status" />
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Not used in any projects yet.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar - Required Materials --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Required Materials</h3>
                @if($product->requiredMaterials->count() > 0)
                    <div class="space-y-3">
                        @foreach($product->requiredMaterials as $material)
                            <a href="{{ route('inventory.show', $material) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600/50 transition">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $material->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $material->sku }}</p>
                                    @if($material->pivot->quantity_required)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Needs {{ number_format($material->pivot->quantity_required, 2) }} {{ $material->unit_of_measure }} per unit</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($material->is_low_stock)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Low Stock</span>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($material->available_quantity, 0) }} {{ $material->unit_of_measure }} available</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No materials assigned.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
