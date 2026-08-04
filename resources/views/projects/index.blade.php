<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Project Management</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage kitchen projects, progress, materials, and teams</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- View Toggle -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                        <a href="{{ route('projects.index', ['view' => 'grid'] + request()->except('view')) }}"
                           class="p-2 rounded {{ ($view ?? 'grid') === 'grid' ? 'bg-white dark:bg-gray-700 shadow-sm' : '' }}"
                           title="Grid View">
                            <svg class="w-5 h-5 {{ ($view ?? 'grid') === 'grid' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </a>
                        <a href="{{ route('projects.index', ['view' => 'table'] + request()->except('view')) }}"
                           class="p-2 rounded {{ ($view ?? 'grid') === 'table' ? 'bg-white dark:bg-gray-700 shadow-sm' : '' }}"
                           title="Table View">
                            <svg class="w-5 h-5 {{ ($view ?? 'grid') === 'table' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </a>
                    </div>
                    <button x-data @click="$dispatch('open-modal', 'new-project')" class="inline-flex items-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Project
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Status Filter Tabs --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                @php
                    $statuses = [
                        '' => 'All',
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'in_production' => 'In Production',
                        'delayed' => 'Delayed',
                        'finished' => 'Finished',
                        'delivered' => 'Delivered',
                    ];
                    $currentStatus = request('status', '');
                @endphp

                @foreach($statuses as $value => $label)
                    <a href="{{ route('projects.index', ['status' => $value]) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border
                              {{ $currentStatus === $value
                                  ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if(($view ?? 'grid') === 'table')
            {{-- Table View --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Team</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse(($projects ?? collect()) as $project)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $project->order_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Created {{ $project->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $project->client->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                                                'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                'in_production' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                'delayed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                'finished' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                                'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            ];
                                            $statusColor = $statusColors[$project->status ?? 'draft'] ?? $statusColors['draft'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $project->status ?? 'Draft')) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $project->products->count() }} item(s)
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        @if($project->members && $project->members->count() > 0)
                                            {{ $project->members->take(2)->pluck('first_name')->join(', ') }}
                                            @if($project->members->count() > 2)
                                                <span class="text-gray-400">+{{ $project->members->count() - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $project->delivery_date ? $project->delivery_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            @can('update', $project)
                                                <a href="{{ route('projects.edit', $project) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                            @endcan
                                            @can('delete', $project)
                                                <x-confirm-delete
                                                    :action="route('projects.destroy', $project)"
                                                    message="Are you sure you want to delete this project? All associated data including attachments and team assignments will be permanently removed."
                                                    title="Delete Project"
                                                    buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </x-confirm-delete>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No projects</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new project.</p>
                                        <div class="mt-6">
                                            <button x-data @click="$dispatch('open-modal', 'new-project')" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                New Project
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse (($projects ?? collect()) as $project)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                    {{-- Status Badge --}}
                    <div class="mb-3">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                                'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'in_production' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'delayed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'finished' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            ];
                            $statusColor = $statusColors[$project->status ?? 'draft'] ?? $statusColors['draft'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status ?? 'Draft')) }}
                        </span>
                    </div>

                    {{-- Project Name --}}
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                        {{ $project->name }}
                    </h3>

                    {{-- Client Name --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {{ $project->client->name ?? 'N/A' }}
                    </p>

                    {{-- No of Products --}}
                    <div class="mb-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">No of Products</p>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $project->products->count() }}
                        </p>
                    </div>

                    {{-- Products List --}}
                    <div class="mb-3">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Products</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            @if($project->products && $project->products->count() > 0)
                                @foreach($project->products as $index => $product)
                                    {{ $product->name }} x{{ $product->pivot->quantity }}{{ $loop->last ? '' : ', ' }}
                                @endforeach
                            @else
                                <span class="text-gray-400 italic">No products assigned</span>
                            @endif
                        </p>
                    </div>

                    {{-- Team Members --}}
                    <div class="mb-3">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Team</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            @if($project->members && $project->members->count() > 0)
                                {{ $project->members->pluck('full_name')->join(', ') }}
                            @else
                                Rajesh Kumar, Amit Patel
                            @endif
                        </p>
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-3">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Deadline</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $project->delivery_date ? $project->delivery_date->format('d-m-Y') : '26-02-2026' }}
                        </p>
                    </div>

                    {{-- Attachment --}}
                    @if($project->attachments && $project->attachments->count() > 0)
                        <div class="mb-3">
                            @foreach($project->attachments->take(3) as $attachment)
                                <a href="{{ route('projects.attachments.download', [$project, $attachment]) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center mb-1 min-w-0" title="Download {{ $attachment->file_name }}">
    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="truncate">{{ $attachment->file_name }}</span>
</a>
                            @endforeach
                            @if($project->attachments->count() > 3)
                                <span class="text-xs text-gray-500 dark:text-gray-400">+{{ $project->attachments->count() - 3 }} more</span>
                            @endif
                        </div>
                    @else
                        <div class="mb-3">
                            <p class="text-sm text-gray-400 dark:text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                No attachments
                            </p>
                        </div>
                    @endif

                    {{-- Created Date --}}
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Created on {{ $project->created_at?->format('d-m-Y') ?? 'N/A' }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('projects.show', $project) }}" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-blue-900/30 rounded-lg transition-all" title="View Details">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all" title="Edit Project">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        @endcan
                        @can('delete', $project)
                            <x-confirm-delete
                                :action="route('projects.destroy', $project)"
                                message="Are you sure you want to delete this project? All associated data including attachments and team assignments will be permanently removed."
                                title="Delete Project"
                                buttonClass="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </x-confirm-delete>
                        @endcan
                    </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No projects</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new project.</p>
                            <div class="mt-6">
                                <button x-data @click="$dispatch('open-modal', 'new-project')" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    New Project
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Pagination --}}
        @if(isset($projects) && method_exists($projects, 'links'))
            <div class="mt-6">
                {{ $projects->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- New Project Modal --}}
    <x-modal name="new-project" :show="$errors->hasAny(['name', 'client_id', 'status', 'proposal_signed_date', 'delivery_date', 'production_deadline', 'products', 'products.*', 'members', 'members.*', 'attachments', 'attachments.*'])" maxWidth="3xl">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">New Project</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Fill in the details below to create a new project</p>

            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-6"
            x-data="{ nameTaken: false }"
            x-on:submit="if (nameTaken) { $event.preventDefault(); }">
            @csrf

                {{-- Basic Information --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required
        x-on:input.debounce.400ms="
            if (! $event.target.value) { nameTaken = false; return; }
            fetch('{{ route('projects.check-name') }}?name=' + encodeURIComponent($event.target.value))
                .then(r => r.json())
                .then(data => nameTaken = data.exists);
        "
        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
    <p x-show="nameTaken" x-cloak class="text-red-500 text-xs mt-1">This project name is already taken. Please choose another name.</p>
    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client Name</label>
                            <select name="client_id" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">Select client</option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Use "Change Status" after creation to move to production stages.<br>
                                <span class="text-red-500">Note: Projects automatically marked as "Delayed" in real-time if delivery date passes.</span>
                            </p>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Equipment --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Equipment</h3>
                    <div x-data="{ products: [{product: '', quantity: 1}] }">
                        <template x-for="(product, index) in products" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Product</label>
                                    <select :name="'products['+index+'][product_id]'" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                        <option value="">Select a product</option>
                                        @foreach($productsList ?? [] as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No of Product</label>
                                    <input type="number" :name="'products['+index+'][quantity]'" min="1" x-model="product.quantity" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="products.push({product: '', quantity: 1})" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            + Add More
                        </button>
                    </div>
                </div>

                {{-- Project Timeline --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Project Timeline</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proposal Signed</label>
                            <input type="date" name="proposal_signed_date" value="{{ old('proposal_signed_date') }}" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            @error('proposal_signed_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Date</label>
                            <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            @error('delivery_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Attachments --}}
                <div x-data="{
                    files: [],
                    isDragging: false,
                    updateFiles(fileList) {
                        this.files = Array.from(fileList);
                    }
                }">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Attachment</h3>
                    <div
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; updateFiles($event.dataTransfer.files)"
                        :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700'"
                        class="border-2 border-dashed rounded-lg p-6 text-center transition-colors">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Drag & Drop to choose files</p>
                        <input
                            type="file"
                            name="attachments[]"
                            multiple
                            class="hidden"
                            id="file-upload"
                            x-ref="fileInput"
                            @change="updateFiles($event.target.files)">
                        <label for="file-upload" class="mt-3 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Choose Files
                        </label>
                        <template x-if="files.length > 0">
                            <div class="mt-4 text-left">
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Selected files:</p>
                                <ul class="space-y-1">
                                    <template x-for="file in files" :key="file.name">
                                        <li class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span x-text="file.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Assign Team Members --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Assign Team Members</h3>
                    @if(isset($users) && $users->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                            @foreach($users as $user)
                                <label class="flex items-center space-x-2 p-2 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <input type="checkbox" name="members[]" value="{{ $user->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <div class="w-8 h-8 flex-shrink-0 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 flex items-center justify-center text-xs font-medium">
                                            {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $user->full_name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No users available. Please add users first.</p>
                    @endif
                    @error('members') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('attachments') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="nameTaken" :class="nameTaken ? 'opacity-50 cursor-not-allowed' : ''" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg transition-all">
    Create Project
</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
