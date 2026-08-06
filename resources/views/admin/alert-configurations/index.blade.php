<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Alert Configurations') }}</h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-md">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @foreach ($alertConfigurations as $config)
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.alert-configurations.update', $config) }}" x-data="{ emails: {{ json_encode($config->always_notify_emails ?? []) }}, newEmail: '' }">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $config->alert_type)) }}</h3>
                        </div>
                        <div>
                            <x-input-label for="threshold_{{ $config->id }}" value="Threshold" />
                            <x-text-input id="threshold_{{ $config->id }}" name="threshold_value" type="number" step="0.01" class="mt-1 w-24" :value="$config->threshold_value" />
                        </div>
                        <div>
                            <x-input-label value="Notify roles" />
                            <div class="mt-1 flex flex-wrap gap-3">
                                @foreach($roles as $role)
                                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="notify_roles[]" value="{{ $role->slug }}"
                                            {{ in_array($role->slug, $config->notify_roles ?? []) ? 'checked' : '' }}
                                            class="rounded border-gray-300 dark:border-gray-700 text-indigo-600">
                                        {{ $role->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="notify_via_email" value="0">
                            <input type="checkbox" name="notify_via_email" value="1" id="email_{{ $config->id }}" {{ $config->notify_via_email ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-indigo-600">
                            <label for="email_{{ $config->id }}" class="text-sm text-gray-700 dark:text-gray-300">Email</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_enabled" value="0">
                            <input type="checkbox" name="is_enabled" value="1" id="enabled_{{ $config->id }}" {{ $config->is_enabled ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-indigo-600">
                            <label for="enabled_{{ $config->id }}" class="text-sm text-gray-700 dark:text-gray-300">Enabled</label>
                        </div>
                        <x-primary-button class="text-xs">Save</x-primary-button>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <x-input-label value="Always notify these emails" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sent regardless of role - useful for someone who isn't a system user, or should always be looped in. Only sent if "Email" above is checked.</p>
                        <div class="flex flex-wrap gap-2 mb-2" x-show="emails.length">
                            <template x-for="(email, index) in emails" :key="index">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                                    <span x-text="email"></span>
                                    <button type="button" @click="emails.splice(index, 1)" class="text-gray-400 hover:text-red-500">&times;</button>
                                    <input type="hidden" name="always_notify_emails[]" :value="email">
                                </span>
                            </template>
                        </div>
                        <input type="email" x-model="newEmail"
                            @keydown.enter.prevent="if (newEmail.trim()) { emails.push(newEmail.trim()); newEmail = ''; }"
                            @keydown.comma.prevent="if (newEmail.trim()) { emails.push(newEmail.trim()); newEmail = ''; }"
                            placeholder="Type an email and press Enter"
                            class="block w-full max-w-sm text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <x-input-error :messages="$errors->get('always_notify_emails.*')" class="mt-2" />
                    </div>
                </form>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if(isset($alertConfigurations) && $alertConfigurations instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $alertConfigurations->withQueryString()->links() }}
        </div>
    @endif
</x-app-layout>
