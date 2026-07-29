<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('teams.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $team->name }}
                    </h2>
                    @if($team->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $team->description }}</p>
                    @endif
                </div>
            </div>
            <x-status-badge :status="$team->is_active ? 'active' : 'inactive'" />
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Members --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Members ({{ $team->users->count() }})
                    </h3>
                </div>

                {{-- Add Member Form --}}
                <form method="POST" action="{{ route('teams.members.store', $team) }}" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <x-input-label for="user_id" :value="__('Add Member')" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select User</option>
                                @foreach($availableUsers ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-40">
                            <x-input-label for="role_in_team" :value="__('Role')" />
                            <select id="role_in_team" name="role_in_team" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="member">Member</option>
                                <option value="lead">Lead</option>
                            </select>
                        </div>
                        <x-primary-button>
                            Add
                        </x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                </form>

                {{-- Members List --}}
                @if($team->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($team->users as $member)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">{{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->full_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $member->email }}
                                            @if($member->pivot->role_in_team)
                                                <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
                                                    {{ ucfirst($member->pivot->role_in_team) }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <button x-data @click="$dispatch('open-modal', 'remove-member-{{ $member->id }}')" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm transition">
                                    Remove
                                </button>
                            </div>

                            <!-- Remove Member Modal -->
                            <x-modal name="remove-member-{{ $member->id }}" :show="false" maxWidth="md">
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-4">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Remove Team Member</h2>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $member->full_name }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Are you sure you want to remove <strong>{{ $member->full_name }}</strong> from this team?
                                        </p>
                                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                This member will no longer have access to team projects and resources.
                                            </p>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('teams.members.destroy', [$team, $member]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="flex justify-end gap-3">
                                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-colors">
                                                Remove Member
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No members yet. Add members using the form above.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Assigned Projects --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assigned Projects</h3>
                @if($team->projects->count() > 0)
                    <div class="space-y-2">
                        @foreach($team->projects as $project)
                            <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600/50 transition">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client->name ?? 'N/A' }}</p>
                                </div>
                                <x-status-badge :status="$project->status" />
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">No projects assigned.</p>
                @endif
            </div>

            {{-- Team Info --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Team Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $team->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Members</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $team->users->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Projects</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $team->projects->whereIn('status', ['confirmed', 'in_production'])->count() }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6 border border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
                <x-confirm-delete :action="route('teams.destroy', $team)" message="Are you sure you want to delete this team? Members will be unassigned.">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Team
                </x-confirm-delete>
            </div>
        </div>
    </div>
</x-app-layout>
