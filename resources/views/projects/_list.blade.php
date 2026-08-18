@if(($view ?? 'grid') === 'table')
    {{-- Table View --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        @php
                            $sortLink = fn (string $column) => route('projects.index', array_merge(
                                request()->except(['sort', 'direction', 'page']),
                                ['sort' => $column, 'direction' => (request('sort') === $column && request('direction', 'desc') === 'asc') ? 'desc' : 'asc']
                            ));
                            $sortIcon = fn (string $column) => request('sort') === $column
                                ? (request('direction', 'desc') === 'asc' ? '&uarr;' : '&darr;')
                                : '';
                        @endphp
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ $sortLink('order_no') }}" wire:navigate class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">Order No {!! $sortIcon('order_no') !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ $sortLink('name') }}" wire:navigate class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">Project Name {!! $sortIcon('name') !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ $sortLink('status') }}" wire:navigate class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">Status {!! $sortIcon('status') !!}</a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Team</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <a href="{{ $sortLink('delivery_date') }}" wire:navigate class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">Delivery Date {!! $sortIcon('delivery_date') !!}</a>
                        </th>
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
                                        'design' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                                        'in_production' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                        'delayed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                        'inspection' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                        'finished' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'delivered' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
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
                        'design' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                        'in_production' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                        'delayed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        'inspection' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                        'finished' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'delivered' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                    ];
                    $statusColor = $statusColors[$project->status ?? 'draft'] ?? $statusColors['draft'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status ?? 'Draft')) }}
                </span>
            </div>

            {{-- Project Name --}}
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1 truncate" title="{{ $project->name }}">
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
