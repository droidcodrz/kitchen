<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Teams</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your workforce</p>
                </div>
                <div class="flex items-center gap-3">
                    <button x-data @click="$dispatch('open-modal', 'new-team')" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-150">
                        New Team +
                    </button>
                    <button x-data @click="$dispatch('open-modal', 'add-member')" class="inline-flex items-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all duration-150">
                        Add Member +
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Team List --}}
        <div class="space-y-6">
            @forelse (($teams ?? collect()) as $team)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                    {{-- Team Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $team->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $team->users ? $team->users->count() : 0 }} members . {{ $team->projects ? $team->projects->count() : 0 }} active projects
                            </p>
                        </div>
                        {{-- Action Icons --}}
                        <div class="flex items-center gap-3">
                            <a href="{{ route('teams.edit', $team) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <x-confirm-delete
                                :action="route('teams.destroy', $team)"
                                message="Are you sure you want to delete this team? Members will be unassigned."
                                title="Delete Team"
                                buttonClass="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </x-confirm-delete>
                        </div>
                    </div>

                    {{-- Team Members Row --}}
                    <div class="flex flex-wrap gap-4">
                        @if($team->users && $team->users->count() > 0)
                            @foreach($team->users as $user)
                                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 min-w-[200px]">
                                    {{-- Avatar --}}
                                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $user->full_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->role->name ?? 'Member' }}</p>
                                    </div>

                                    {{-- Icons --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($user->email)
                                            <a href="mailto:{{ $user->email }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="{{ $user->email }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if($user->phone_number)
                                            <a href="tel:{{ $user->phone_number }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="{{ $user->phone_number }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No members in this team yet.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No teams</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new team.</p>
                    <div class="mt-6">
                        <button x-data @click="$dispatch('open-modal', 'new-team')" class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 active:bg-gray-950 dark:active:bg-gray-500 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition-all">
                            New Team +
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($teams) && method_exists($teams, 'links'))
            <div class="mt-6">
                {{ $teams->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- New Team Modal --}}
    <x-modal name="new-team" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">New Team</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Create a new team</p>

            <form method="POST" action="{{ route('teams.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Team Name</label>
                    <input type="text" name="name" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="3" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition-all">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Add Member Modal --}}
    <x-modal name="add-member" :show="$errors->hasAny(['first_name', 'last_name', 'email', 'password', 'role_id', 'team_id'])" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Add New Member</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Add a new member to your workforce</p>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role_id" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select role</option>
                            @foreach($roles ?? [] as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign to Team</label>
                        <select name="team_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select team</option>
                            @foreach($teams ?? [] as $team)
                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('team_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition-all">
                        Add Member
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
