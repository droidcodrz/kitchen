<x-app-layout>
    <x-slot name="title">Dropdown Options</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('settings.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dropdown Options') }}
                </h2>
            </div>
            <button x-data @click="$dispatch('open-modal', 'add-option')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Option
            </button>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-md">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/50 text-red-700 dark:text-red-300 p-4 rounded-md">{{ session('error') }}</div>
    @endif

    {{-- Type Tabs --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                @foreach($types as $typeKey => $typeLabel)
                    <a href="{{ route('admin.dropdown-options.index', ['type' => $typeKey]) }}"
                       wire:navigate
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $currentType === $typeKey ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        {{ $typeLabel }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div id="options-list">
        @include('admin.dropdown-options._list')
    </div>

    {{-- Add Option Modal --}}
    <x-modal name="add-option" :show="false" maxWidth="lg">
        <div class="p-6" x-data="addOptionForm()">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Add Option</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add a new option for <strong>{{ $types[$currentType] ?? $currentType }}</strong></p>

            <form @submit.prevent="submit($event)" class="space-y-4">
                <input type="hidden" name="type" value="{{ $currentType }}">

                <div x-show="generalError" x-cloak class="rounded-md bg-red-50 dark:bg-red-900/50 p-3 text-sm text-red-800 dark:text-red-200" x-text="generalError"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Value <span class="text-red-500">*</span></label>
                    <input type="text" name="value" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm" placeholder="The stored value (e.g. Stainless Steel)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This is the actual value saved in the database.</p>
                    <p x-show="errors.value" x-text="errors.value && errors.value[0]" class="text-red-500 text-xs mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label <span class="text-red-500">*</span></label>
                    <input type="text" name="label" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm" placeholder="The display label (e.g. Stainless Steel)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This is what users see in the dropdown.</p>
                    <p x-show="errors.label" x-text="errors.label && errors.label[0]" class="text-red-500 text-xs mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-50 cursor-not-allowed' : ''" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg transition-all">
                        <span x-text="submitting ? 'Adding...' : 'Add Option'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function addOptionForm() {
        return {
            errors: {},
            generalError: '',
            submitting: false,

            async submit(event) {
                this.errors = {};
                this.generalError = '';
                this.submitting = true;

                try {
                    const response = await fetch('{{ route('admin.dropdown-options.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: new FormData(event.target),
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.errors = data.errors || {};
                        this.generalError = data.errors ? '' : (data.message || '');
                        this.submitting = false;
                        return;
                    }

                    if (!response.ok) {
                        this.generalError = 'Something went wrong. Please try again.';
                        this.submitting = false;
                        return;
                    }

                    event.target.reset();
                    this.$dispatch('close');

                    const listResponse = await fetch(window.location.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const html = await listResponse.text();
                    document.getElementById('options-list').innerHTML = html;
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
