@props([
    'action',
    'title' => 'Confirm Deletion',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'buttonText' => 'Delete',
    'buttonClass' => '',
    // When deletion isn't allowed, say so on the trigger itself. Opening a
    // dialog only to present a dead button tells the user nothing they could
    // not have been told before clicking.
    'disabled' => false,
    'disabledReason' => 'This item cannot be deleted.',
])

<div x-data="{ showModal: false, busy: false }" {{ $attributes }}>
    {{-- Trigger --}}
    @if($disabled)
        <button type="button" disabled title="{{ $disabledReason }}"
                class="{{ $buttonClass ?: 'inline-flex items-center px-3 py-2 text-sm font-medium transition' }} opacity-40 cursor-not-allowed">
            {{ $slot }}
        </button>
    @else
        <button @click="showModal = true" type="button" class="{{ $buttonClass ?: 'inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition' }}">
            {{ $slot }}
        </button>
    @endif

    {{-- Modal Overlay --}}
    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity" @click="showModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{-- While the delete is in flight the button says so.
                         It used to just grey out with a "not allowed" cursor,
                         which read as "this action is blocked" rather than
                         "working on it". --}}
                    {{-- busy is set on the next tick, never inside the submit
                         handler itself: disabling the submit button while the
                         event is still being dispatched cancels the submission
                         outright in some browsers, so the Delete button simply
                         did nothing. Same reason the global guard in the layout
                         defers its own disabling. --}}
                    <form method="POST" action="{{ $action }}" @submit="setTimeout(() => busy = true, 0)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" :disabled="busy"
                                class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto disabled:opacity-75 disabled:cursor-wait">
                            <svg x-show="busy" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="busy ? 'Deleting...' : @js($buttonText)">{{ $buttonText }}</span>
                        </button>
                    </form>
                    <button @click="showModal = false" type="button" :disabled="busy"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto disabled:opacity-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
