<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $project->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order #{{ $project->order_no }}</p>
                     @if(!empty($project->labels))

                        <div class="flex flex-wrap gap-1 mt-1">

                            @foreach($project->labels as $label)

                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full">{{ $label }}</span>

                            @endforeach

                        </div>

                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <x-status-badge :status="$project->status" />
                @can('changeStatus', $project)
                    <x-project-status-transition :project="$project" />
                @endcan
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Project Details --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->client->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Project Manager</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->projectManager->full_name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Proposal Signed</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->proposal_signed_date?->format('M d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->delivery_date?->format('M d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Production Deadline</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->production_deadline?->format('M d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($project->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->description }}</dd>
                        </div>
                    @endif
                    @if($project->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Products --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Products / Equipment</h3>
                @if($project->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($project->products as $product)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $product->name }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-center">{{ $product->pivot->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">${{ number_format($product->pivot->unit_price_at_time ?? $product->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No products assigned to this project.</p>
                @endif
            </div>

            {{-- Attachments --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Attachments</h3>
                @if($project->attachments->count() > 0)
                    <div class="space-y-2">
                        @foreach($project->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $attachment->original_name ?? $attachment->file_name ?? 'Attachment' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('projects.attachments.download', [$project, $attachment]) }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm font-medium" title="Download">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download
                                    </a>
                                    <x-confirm-delete
                                        :action="route('projects.attachments.destroy', [$project, $attachment])"
                                        message="Are you sure you want to delete this attachment? This action cannot be undone."
                                        title="Delete Attachment"
                                        buttonClass="text-red-600 dark:text-red-400 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </x-confirm-delete>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No attachments yet.</p>
                @endif
            </div>
            {{-- Activity --}}

            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Activity</h3>

                @if($project->activities->count() > 0)

                    <ul class="space-y-4">

                        @foreach($project->activities as $activity)

                            @php

                                $changedFields = collect(($activity->properties['new'] ?? []))->keys()

                                    ->reject(fn ($field) => in_array($field, ['updated_at']));

                            @endphp

                            <li class="flex gap-3">

                                <div class="flex-shrink-0 mt-1">

                                    <span class="flex h-2.5 w-2.5 rounded-full {{ $activity->action === 'created' ? 'bg-green-500' : ($activity->action === 'deleted' ? 'bg-red-500' : 'bg-indigo-500') }}"></span>

                                </div>

                                <div class="flex-1 min-w-0">

                                    <p class="text-sm text-gray-900 dark:text-gray-100">

                                        <span class="font-medium">{{ $activity->user->full_name ?? 'System' }}</span>

                                        @if($activity->action === 'created')

                                            created this project

                                        @elseif($activity->action === 'deleted')

                                            deleted this project

                                        @elseif($changedFields->contains('status'))

                                            changed status from

                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $activity->properties['old']['status'] ?? '')) }}</span>

                                            to

                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $activity->properties['new']['status'] ?? '')) }}</span>

                                        @elseif($changedFields->isNotEmpty())

                                            updated {{ $changedFields->map(fn ($f) => str_replace('_', ' ', $f))->implode(', ') }}

                                        @else

                                            updated this project

                                        @endif

                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $activity->created_at->format('M d, Y g:i A') }}</p>

                                </div>

                            </li>

                        @endforeach

                    </ul>

                @else

                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No activity recorded yet.</p>

                @endif

            </div>
        </div>

        {{-- Milestones --}}
        <div class="space-y-6">
            {{-- Status Change --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <!-- <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Change Status</h3>
                <form method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mb-3">
                        @foreach(['draft','confirmed','in_production','delayed','finished','delivered'] as $s)
                            <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button class="w-full justify-center">
                        Update Status
                    </x-primary-button>
                </form> -->
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Milestones</h3>

 

                @if($project->milestones->count() > 0)

                    <ul class="space-y-3 mb-4">

                        @foreach($project->milestones as $milestone)

                            <li class="flex items-start gap-3">

                                <form method="POST" action="{{ route('projects.milestones.update', [$project, $milestone]) }}" class="mt-0.5">

                                    @csrf

                                    @method('PATCH')

                                    <input type="hidden" name="toggle_complete" value="1">

                                    <button type="submit" class="flex-shrink-0 w-4 h-4 rounded border-2 flex items-center justify-center {{ $milestone->is_complete ? 'bg-green-500 border-green-500' : 'border-gray-300 dark:border-gray-600' }}" title="{{ $milestone->is_complete ? 'Mark as not complete' : 'Mark as complete' }}">

                                        @if($milestone->is_complete)

                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>

                                        @endif

                                    </button>

                                </form>

                                <div class="flex-1 min-w-0">

                                    <p class="text-sm {{ $milestone->is_complete ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-gray-100' }}">{{ $milestone->title }}</p>

                                    @if($milestone->due_date)

                                        <p class="text-xs {{ $milestone->is_overdue ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">

                                            Due {{ $milestone->due_date->format('M d, Y') }}{{ $milestone->is_overdue ? ' (overdue)' : '' }}

                                        </p>

                                    @endif

                                </div>

                               <x-confirm-delete
                                :action="route('projects.milestones.destroy', [$project, $milestone])"
                                title="Delete Milestone"
                                message="Are you sure you want to delete this milestone? This action cannot be undone."
                                buttonClass="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </x-confirm-delete>

                            </li>

                        @endforeach
                    </ul>
                    @else

                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2 mb-4">No milestones yet.</p>

                @endif
                 <form method="POST" action="{{ route('projects.milestones.store', $project) }}" class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">

                    @csrf

                    <input type="text" name="title" required placeholder="Add a milestone..." class="flex-1 min-w-0 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

                    <input type="date" name="due_date" class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

                    <button type="submit" class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>

                    </button>

                </form>
            </div>

            {{-- Team Members --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Team Members</h3>
                @if($project->members->count() > 0)
                    <div class="space-y-3">
                        @foreach($project->members as $member)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">{{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->role->name ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">No team members assigned.</p>
                @endif
            </div>

            {{-- Teams --}}
            @if($project->teams->count() > 0)
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assigned Teams</h3>
                    <div class="space-y-2">
                        @foreach($project->teams as $team)
                            <a href="{{ route('teams.show', $team) }}" class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600/50 transition">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $team->users->count() }} members</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6 border border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
                <x-confirm-delete :action="route('projects.destroy', $project)" message="Are you sure you want to delete this project? All associated data will be permanently removed.">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Project
                </x-confirm-delete>
            </div>
        </div>
    </div>
</x-app-layout>
