@props(['title', 'value', 'subtitle' => '', 'color' => 'indigo', 'icon' => null])

@php
    $colorClasses = [
        'indigo' => 'bg-indigo-500',
        'red' => 'bg-red-500',
        'amber' => 'bg-amber-500',
        'green' => 'bg-green-500',
        'blue' => 'bg-blue-500',
        'purple' => 'bg-purple-500',
        'gray' => 'bg-gray-500',
    ];
    $bgColor = $colorClasses[$color] ?? 'bg-indigo-500';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg']) }}>
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="{{ $bgColor }} rounded-lg p-3">
                    @if($icon)
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $icon !!}
                        </svg>
                    @else
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    @endif
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $value }}
                        </div>
                    </dd>
                    @if($subtitle)
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $subtitle }}
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
