<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $storageLocation->name }}</h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-md">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Code</dt><dd class="text-gray-900 dark:text-gray-100">{{ $storageLocation->code ?? 'N/A' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt><dd class="text-gray-900 dark:text-gray-100">{{ $storageLocation->description ?? 'N/A' }}</dd></div>
        </dl>
        <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-gray-100">Inventory Items</h3>
        <ul class="mt-2 space-y-1">
            @forelse ($storageLocation->inventoryItems as $item)
                <li class="text-sm text-gray-700 dark:text-gray-300">{{ $item->name }} ({{ $item->sku }})</li>
            @empty
                <li class="text-sm text-gray-500">No items in this location</li>
            @endforelse
        </ul>
    </div>
</x-app-layout>
