<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Alerts</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Low stock, deadlines, project updates, and inventory alerts</p>
                </div>
                @if(isset($notifications) && $notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-150">
                            Mark All Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-4">
            @forelse (($notifications ?? collect()) as $notification)
                @php
                    $data = $notification->data ?? [];
                    $type = $data['type'] ?? 'info';
                    $title = $data['title'] ?? $data['message'] ?? 'Notification';
                    $description = $data['description'] ?? $data['body'] ?? '';
                    $date = $notification->created_at->format('d M Y');
                    $isUnread = is_null($notification->read_at);

                    $iconColor = match($type) {
                        'low_stock', 'error', 'critical' => 'text-red-500',
                        'success', 'new_item', 'new_products' => 'text-green-500',
                        'warning', 'deadline' => 'text-orange-500',
                        'delayed', 'overdue' => 'text-yellow-500',
                        default => 'text-blue-500',
                    };
                    $bgColor = match($type) {
                        'low_stock', 'error', 'critical' => 'bg-red-50 dark:bg-red-900/10',
                        'success', 'new_item', 'new_products' => 'bg-green-50 dark:bg-green-900/10',
                        'warning', 'deadline' => 'bg-orange-50 dark:bg-orange-900/10',
                        'delayed', 'overdue' => 'bg-yellow-50 dark:bg-yellow-900/10',
                        default => 'bg-blue-50 dark:bg-blue-900/10',
                    };
                    $borderColor = match($type) {
                        'low_stock', 'error', 'critical' => 'border-red-200 dark:border-red-800',
                        'success', 'new_item', 'new_products' => 'border-green-200 dark:border-green-800',
                        'warning', 'deadline' => 'border-orange-200 dark:border-orange-800',
                        'delayed', 'overdue' => 'border-yellow-200 dark:border-yellow-800',
                        default => 'border-blue-200 dark:border-blue-800',
                    };
                @endphp

                <div class="bg-white dark:bg-gray-900 border {{ $borderColor }} rounded-lg p-5 {{ $bgColor }} hover:shadow-md transition-shadow duration-200 {{ $isUnread ? 'ring-1 ring-inset ring-gray-300 dark:ring-gray-700' : 'opacity-80' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            @if(in_array($type, ['low_stock', 'error', 'critical']))
                                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @elseif(in_array($type, ['success', 'new_item', 'new_products']))
                                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif(in_array($type, ['deadline', 'warning']))
                                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $title }}
                            </h3>
                            @if($description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $description }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $date }}
                            </p>
                        </div>

                        {{-- Mark as Read Button --}}
                        @if($isUnread)
                            <div class="flex-shrink-0">
                                <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="Mark as read">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No alerts</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up! No new alerts at this time.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($notifications) && method_exists($notifications, 'links'))
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
