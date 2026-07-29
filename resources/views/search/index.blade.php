<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Search Results
            @if($query)
                <span class="text-gray-500 dark:text-gray-400">for "{{ $query }}"</span>
            @endif
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(strlen($query) < 2)
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Enter at least 2 characters to search</p>
            </div>
        @elseif(
            $results['products']->isEmpty() &&
            $results['inventory']->isEmpty() &&
            $results['projects']->isEmpty()
        )
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No results found for "{{ $query }}"</p>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">Try different keywords or check your spelling</p>
            </div>
        @else
            {{-- Products Results --}}
            @if($results['products']->count() > 0)
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Products ({{ $results['products']->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($results['products'] as $product)
                            <a href="{{ route('products.show', $product) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $product->name }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($product->item_label)
                                                <span class="text-xs font-mono text-blue-600 dark:text-blue-400">{{ $product->item_label }}</span>
                                            @endif
                                            @if($product->sku)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</span>
                                            @endif
                                            @if($product->category)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $product->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Inventory Results --}}
            @if($results['inventory']->count() > 0)
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Inventory ({{ $results['inventory']->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($results['inventory'] as $item)
                            <a href="{{ route('inventory.show', $item) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $item->name }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($item->item_label)
                                                <span class="text-xs font-mono text-blue-600 dark:text-blue-400">{{ $item->item_label }}</span>
                                            @endif
                                            @if($item->sku)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->sku }}</span>
                                            @endif
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Stock: {{ number_format($item->stock_quantity, 0) }}</span>
                                            @if($item->storageLocation)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $item->storageLocation->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Projects Results --}}
            @if($results['projects']->count() > 0)
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Projects ({{ $results['projects']->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($results['projects'] as $project)
                            <a href="{{ route('projects.show', $project) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $project->name }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($project->order_no)
                                                <span class="text-xs font-mono text-blue-600 dark:text-blue-400">{{ $project->order_no }}</span>
                                            @endif
                                            @if($project->client)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->name }}</span>
                                            @endif
                                            <x-status-badge :status="$project->status" />
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
