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
                <form method="POST" action="{{ route('admin.alert-configurations.update', $config) }}">
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
