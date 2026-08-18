<x-app-layout>
    <x-slot name="title">Waste Bin</x-slot>

    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Waste Bin</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Restore or permanently delete items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filter Tabs -->
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                @php
                    $filters = [
                        'all' => 'All Items',
                        'projects' => 'Projects',
                        'products' => 'Products',
                        'categories' => 'Categories',
                        'folders' => 'Folders',
                    ];
                    $currentType = $type ?? 'all';
                @endphp

                @foreach($filters as $value => $label)
                    <a href="{{ route('trash.index', ['type' => $value]) }}"
                       wire:navigate
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border
                              {{ $currentType === $value
                                  ? 'bg-red-600 text-white border-red-600 shadow-sm'
                                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        {{ $label }}
                        @if($value === 'all')
                            ({{ ($counts['projects'] ?? 0) + ($counts['products'] ?? 0) + ($counts['categories'] ?? 0) + ($counts['folders'] ?? 0) }})
                        @else
                            ({{ $counts[$value] ?? 0 }})
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div id="trash-list">
            @include('trash._list')
        </div>
    </div>
</x-app-layout>
