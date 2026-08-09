@props(['status'])

@php
    $colors = [
        'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        'design' => 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
        'in_production' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
        'inspection' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
        'delayed' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'finished' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'delivered' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
        'in_stock' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'out_of_stock' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'active' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    ];

    $labels = [
        'draft' => 'Draft',
        'confirmed' => 'Confirmed',
        'design' => 'Design',
        'in_production' => 'In Production',
        'delayed' => 'Delayed',
        'inspection' => 'Inspection',
        'finished' => 'Finished',
        'delivered' => 'Delivered',
        'in_stock' => 'In Stock',
        'out_of_stock' => 'Out of Stock',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}"]) }}>
    {{ $label }}
</span>
