<x-app-layout>
    <x-slot name="title">Projects</x-slot>

    {{-- Grid/Table preference lives entirely in the browser: switching views
         swaps already-rendered markup instead of re-fetching the page. The
         choice is remembered across navigations via localStorage. --}}
    <script>
        (function () {
            function currentStatusFromUrl() {
                return new URL(window.location).searchParams.get('status') || '';
            }

            function registerProjectStores() {
                if (!window.Alpine) return;

                if (!Alpine.store('projectsView')) {
                    Alpine.store('projectsView', {
                        mode: localStorage.getItem('projectsView') || @json($view ?? 'grid'),
                        set(mode) {
                            this.mode = mode;
                            localStorage.setItem('projectsView', mode);
                        },
                    });
                }

                if (!Alpine.store('projectsFilter')) {
                    Alpine.store('projectsFilter', {
                        status: currentStatusFromUrl(),
                        matches(status) {
                            return this.status === '' || this.status === status;
                        },
                        set(status) {
                            this.status = status;
                            // Keep the URL shareable/reloadable without navigating.
                            const url = new URL(window.location);
                            status ? url.searchParams.set('status', status) : url.searchParams.delete('status');
                            window.history.replaceState({}, '', url);
                        },
                        visibleCount() {
                            return Array.from(document.querySelectorAll('[data-status]'))
                                .filter(el => this.matches(el.dataset.status)).length;
                        },
                    });
                } else {
                    // Already registered from an earlier visit - re-sync to the
                    // status this page was opened with.
                    Alpine.store('projectsFilter').status = currentStatusFromUrl();
                }
            }

            // alpine:init fires only on Alpine's first boot. Arriving here via
            // wire:navigate re-runs this script with Alpine already running, so
            // that event never comes again - registering only on it left the
            // stores undefined, every x-show threw, and the whole list vanished.
            if (window.Alpine) {
                registerProjectStores();
            } else {
                document.addEventListener('alpine:init', registerProjectStores);
            }

            document.addEventListener('livewire:navigated', registerProjectStores);
        })();
    </script>

    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Project Management</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage kitchen projects, progress, materials, and teams</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- View Toggle: purely client-side, no request on switch -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                        <button type="button"
                                @click="$store.projectsView && $store.projectsView.set('grid')"
                                class="p-2 rounded transition-colors"
                                :class="$store.projectsView && $store.projectsView.mode === 'grid' ? 'bg-white dark:bg-gray-700 shadow-sm' : ''"
                                title="Grid View">
                            <svg class="w-5 h-5" :class="$store.projectsView && $store.projectsView.mode === 'grid' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button type="button"
                                @click="$store.projectsView && $store.projectsView.set('table')"
                                class="p-2 rounded transition-colors"
                                :class="$store.projectsView && $store.projectsView.mode === 'table' ? 'bg-white dark:bg-gray-700 shadow-sm' : ''"
                                title="Table View">
                            <svg class="w-5 h-5" :class="$store.projectsView && $store.projectsView.mode === 'table' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
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
        {{-- Search --}}
        <div class="mb-4" x-data="{ q: '{{ request('search', '') }}' }">
            <div class="relative max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="q" placeholder="Search by name, order #, or client..."
                    x-on:input.debounce.400ms="
                        const params = new URLSearchParams(window.location.search);
                        if (q) { params.set('search', q); } else { params.delete('search'); }
                        params.delete('page');
                        Livewire.navigate('{{ route('projects.index') }}?' + params.toString());
                    "
                    class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
        </div>

        {{-- Status Filter Tabs --}}
        @php
            $statuses = [
                '' => 'All',
                'draft' => 'Draft',
                'confirmed' => 'Confirmed',
                'design' => 'Design',
                'in_production' => 'In Production',
                'delayed' => 'Delayed',
                'inspection' => 'Inspection',
                'finished' => 'Finished',
                'delivered' => 'Delivered',
            ];
            $currentStatus = request('status', '');
        @endphp
        {{-- Status tabs filter the already-loaded rows in the browser, so
             switching between them is instant and costs no request at all. --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                @foreach($statuses as $value => $label)
                    <button type="button"
                            @click="$store.projectsFilter && $store.projectsFilter.set('{{ $value }}')"
                            class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border"
                            :class="$store.projectsFilter && $store.projectsFilter.status === '{{ $value }}'
                                   ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                   : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @include('projects._list')
    </div>

    {{-- New Project Modal --}}
    {{-- Reopen on any error at all: an explicit field list silently missed
         nested keys like inventory_items.0.quantity, so the modal closed and
         the user was left with no idea why nothing saved. --}}
    <x-modal name="new-project" :show="$errors->any()" maxWidth="3xl">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">New Project</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Fill in the details below to create a new project</p>

            <div class="mb-4">
                <x-validation-summary />
            </div>

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
                    <div x-data="{ products: [{product_id: '', quantity: 1}] }">
                        <template x-for="(product, index) in products" :key="index">
                            <div class="flex items-end gap-3 mb-3">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Product</label>
                                    <select :name="'products['+index+'][product_id]'" x-model="product.product_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                        <option value="">Select a product</option>
                                        @foreach($productsList ?? [] as $product)
                                            <option value="{{ $product->id }}"
                                                :disabled="products.some((row, i) => i !== index && row.product_id == '{{ $product->id }}')">{{ $product->name }} ({{ trim('Product' . ($product->sku ? ' - ' . $product->sku : '')) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-32">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No of Product</label>
                                    <input type="number" :name="'products['+index+'][quantity]'" min="1" x-model="product.quantity" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>

                                {{-- Added rows need a way back out, same as the full create form. --}}
                                <button type="button" @click="products.splice(index, 1)" x-show="products.length > 1"
                                        title="Remove this line"
                                        class="mb-1 p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="products.push({product_id: '', quantity: 1})" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            + Add More
                        </button>
                    </div>
                </div>

                {{-- Additional Inventory Items - the same section the full create
                     and edit forms carry, so a project started from this modal can
                     also take raw materials that aren't part of a product. --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Additional Inventory Items</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Raw materials used or sold directly on this project, outside of any manufactured product - e.g. raw steel sheet.</p>
                    <div x-data="{ inventoryItems: [{inventory_item_id: '', quantity: 1}] }">
                        <template x-for="(item, index) in inventoryItems" :key="index">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex-1">
                                    <select :name="'inventory_items['+index+'][inventory_item_id]'" x-model="item.inventory_item_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                        <option value="">Select Inventory Item</option>
                                        @foreach($inventoryItems ?? [] as $inventoryItem)
                                            <option value="{{ $inventoryItem->id }}"
                                                :disabled="inventoryItems.some((row, i) => i !== index && row.inventory_item_id == '{{ $inventoryItem->id }}')">{{ $inventoryItem->name }} ({{ trim('Inventory item' . ($inventoryItem->sku ? ' - ' . $inventoryItem->sku : '')) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-32">
                                    <input type="number" :name="'inventory_items['+index+'][quantity]'" min="0.01" step="0.01" x-model="item.quantity" placeholder="Qty" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>

                                <button type="button" @click="inventoryItems.splice(index, 1)" x-show="inventoryItems.length > 1"
                                        title="Remove this line"
                                        class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="inventoryItems.push({inventory_item_id: '', quantity: 1})" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
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

                {{-- Internal Labels --}}
                <div x-data="{ labels: {{ json_encode(old('labels', [])) }}, newLabel: '' }">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Internal Labels</h3>
                    <div class="flex flex-wrap gap-2 mb-2" x-show="labels.length">
                        <template x-for="(label, index) in labels" :key="index">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                                <span x-text="label"></span>
                                <button type="button" @click="labels.splice(index, 1)" class="text-gray-400 hover:text-red-500">&times;</button>
                                <input type="hidden" name="labels[]" :value="label">
                            </span>
                        </template>
                    </div>
                    <input type="text" x-model="newLabel"
                        @keydown.enter.prevent="if (newLabel.trim()) { labels.push(newLabel.trim()); newLabel = ''; }"
                        @keydown.comma.prevent="if (newLabel.trim()) { labels.push(newLabel.trim()); newLabel = ''; }"
                        placeholder="Type a label and press Enter (e.g. Rush Order, VIP Client)"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    @error('labels') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
