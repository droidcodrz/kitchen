<x-app-layout>
    <x-slot name="title">Teams</x-slot>

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
        <div id="teams-list">
            @include('teams._list')
        </div>
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

            <form x-data="addMemberForm()" @submit.prevent="submit($event)" class="space-y-4">
                @csrf

                <div x-show="generalError" x-cloak class="rounded-md bg-red-50 dark:bg-red-900/50 p-3 text-sm text-red-800 dark:text-red-200" x-text="generalError"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                        <input type="text" name="first_name" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <p x-show="errors.first_name" x-text="errors.first_name && errors.first_name[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                        <input type="text" name="last_name" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <p x-show="errors.last_name" x-text="errors.last_name && errors.last_name[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <p x-show="errors.email" x-text="errors.email && errors.email[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="tel" name="phone_number" pattern="^(\+91[\-\s]?)?[6-9]\d{9}$" maxlength="13" placeholder="9876543210" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <p x-show="errors.phone_number" x-text="errors.phone_number && errors.phone_number[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <p x-show="errors.password" x-text="errors.password && errors.password[0]" class="text-red-500 text-xs mt-1"></p>
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
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <p x-show="errors.role_id" x-text="errors.role_id && errors.role_id[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign to Team</label>
                        <select name="team_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select team</option>
                            @foreach($teams ?? [] as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        <p x-show="errors.team_id" x-text="errors.team_id && errors.team_id[0]" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700 focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-50 cursor-not-allowed' : ''" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition-all">
                        <span x-text="submitting ? 'Adding...' : 'Add Member'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function addMemberForm() {
        return {
            errors: {},
            generalError: '',
            submitting: false,

            async submit(event) {
                this.errors = {};
                this.generalError = '';
                this.submitting = true;

                const form = event.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch('{{ route('admin.users.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: formData,
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                        this.submitting = false;
                        return;
                    }

                    if (!response.ok) {
                        this.generalError = 'Something went wrong. Please try again.';
                        this.submitting = false;
                        return;
                    }

                    form.reset();
                    this.$dispatch('close');

                    const listResponse = await fetch('{{ route('teams.index') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const html = await listResponse.text();
                    document.getElementById('teams-list').innerHTML = html;
                } catch (e) {
                    this.generalError = 'Network error. Please try again.';
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
    </script>
</x-app-layout>
