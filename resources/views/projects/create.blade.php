<x-app-layout>
    <x-slot name="title">New Project</x-slot>

    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Project') }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg">
        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="p-6 space-y-8" id="create-project-form">
            @csrf

            <x-validation-summary />

            {{-- Basic Info --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" :value="__('Project Name')" :required="true" />
                        <x-text-input id="name" name="name" type="text" maxlength="191" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Enter project name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="client_id" :value="__('Client')" :required="true" />
                        <select id="client_id" name="client_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_production" {{ old('status') === 'in_production' ? 'selected' : '' }}>In Production</option>
                            <option value="delayed" {{ old('status') === 'delayed' ? 'selected' : '' }}>Delayed</option>
                            <option value="finished" {{ old('status') === 'finished' ? 'selected' : '' }}>Finished</option>
                            <option value="delivered" {{ old('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="project_manager_id" :value="__('Project Manager')" />
                        <select id="project_manager_id" name="project_manager_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Manager</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('project_manager_id')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Project description...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Equipment / Products --}}
            <div x-data="{
                products: {{ json_encode(old('products', [['product_id' => '', 'quantity' => 1]])) }},
                addProduct() {
                    this.products.push({ product_id: '', quantity: 1 });
                },
                removeProduct(index) {
                    if (this.products.length > 1) {
                        this.products.splice(index, 1);
                    }
                }
            }">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Equipment / Products</h3>

                <div class="space-y-3">
                    <template x-for="(item, index) in products" :key="index">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <select :name="'products[' + index + '][product_id]'" x-model="item.product_id" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <input type="number" :name="'products[' + index + '][quantity]'" x-model="item.quantity" min="1" placeholder="Qty" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            </div>
                            <button type="button" @click="removeProduct(index)" x-show="products.length > 1" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addProduct()" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add More
                </button>
                <x-input-error :messages="$errors->get('products')" class="mt-2" />
            </div>

            {{-- Additional Inventory Items --}}
            <div x-data="{
                inventoryItems: {{ json_encode(old('inventory_items', [['inventory_item_id' => '', 'quantity' => 1]])) }},
                addInventoryItem() {
                    this.inventoryItems.push({ inventory_item_id: '', quantity: 1 });
                },
                removeInventoryItem(index) {
                    if (this.inventoryItems.length > 1) {
                        this.inventoryItems.splice(index, 1);
                    }
                }
            }">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Additional Inventory Items</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Raw materials used or sold directly on this project, outside of any manufactured product - e.g. raw steel sheet.</p>

                <div class="space-y-3">
                    <template x-for="(item, index) in inventoryItems" :key="index">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <select :name="'inventory_items[' + index + '][inventory_item_id]'" x-model="item.inventory_item_id" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Inventory Item</option>
                                    @foreach($inventoryItems ?? [] as $inventoryItem)
                                        <option value="{{ $inventoryItem->id }}">{{ $inventoryItem->name }} ({{ $inventoryItem->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <input type="number" :name="'inventory_items[' + index + '][quantity]'" x-model="item.quantity" min="0.01" step="0.01" placeholder="Qty" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            </div>
                            <button type="button" @click="removeInventoryItem(index)" x-show="inventoryItems.length > 1" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addInventoryItem()" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add More
                </button>
                <x-input-error :messages="$errors->get('inventory_items')" class="mt-2" />
            </div>

            {{-- Timeline --}}
            {{-- The two dates constrain each other in the picker itself, so an
                 out-of-order pair can't be chosen in the first place. The same
                 rule is enforced again server-side. --}}
            <div x-data="{ proposal: @js(old('proposal_signed_date')), delivery: @js(old('delivery_date')) }">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Timeline</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="proposal_signed_date" :value="__('Proposal Signed Date')" />
                        <x-text-input id="proposal_signed_date" name="proposal_signed_date" type="date" class="mt-1 block w-full" x-model="proposal" ::max="delivery || null" />
                        <x-input-error :messages="$errors->get('proposal_signed_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="delivery_date" :value="__('Delivery Date')" />
                        <x-text-input id="delivery_date" name="delivery_date" type="date" class="mt-1 block w-full" x-model="delivery" ::min="proposal || null" />
                        <x-input-error :messages="$errors->get('delivery_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="production_deadline" :value="__('Production Deadline')" />
                        <x-text-input id="production_deadline" name="production_deadline" type="date" class="mt-1 block w-full" :value="old('production_deadline')" />
                        <x-input-error :messages="$errors->get('production_deadline')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            <div x-data="attachmentUpload()">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Attachments</h3>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg transition-colors" :class="dragging ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false; handleFiles($event.dataTransfer.files)">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="attachments" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                                <span>Upload files</span>
                                <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept=".pdf,.png,.jpg,.jpeg,.dwg,.dxf,.doc,.docx,.mp4,.mov,.avi,.webm,.mkv" x-ref="fileInput" @change="handleFiles($event.target.files)" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, images, CAD, Word docs, or video - up to {{ $uploadMaxLabel }} each, {{ $postMaxLabel }} total</p>
                        <template x-if="acceptedNames.length > 0">
                            <p class="text-xs text-green-600 dark:text-green-400" x-text="acceptedNames.join(', ')"></p>
                        </template>
                    </div>
                </div>
                <template x-if="error">
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></p>
                </template>
                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
            </div>

            {{-- Team Members --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Team Members</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($users ?? [] as $user)
                        <label class="relative flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($user->id, old('members', [])) ? 'checked' : '' }} />
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->role->name ?? '' }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('members')" class="mt-2" />
            </div>

            {{-- Notes --}}
            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Create Project
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function attachmentUpload() {
            const allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'dwg', 'dxf', 'doc', 'docx', 'mp4', 'mov', 'avi', 'webm', 'mkv'];

            // The real ceilings PHP will enforce on this server. Checking against
            // these (rather than a hardcoded number) is what keeps an oversized
            // upload from being sent at all - once it is sent, PHP aborts the
            // request body and the browser just shows a connection error.
            const maxBytes = @json($uploadMaxBytes);
            const maxTotalBytes = @json($postMaxBytes);
            const maxLabel = @json($uploadMaxLabel);
            const maxTotalLabel = @json($postMaxLabel);

            return {
                dragging: false,
                error: '',
                acceptedNames: [],

                handleFiles(fileList) {
                    const files = Array.from(fileList);
                    const valid = [];
                    const rejected = [];
                    let total = 0;

                    for (const file of files) {
                        const ext = file.name.split('.').pop().toLowerCase();

                        if (!allowedExtensions.includes(ext)) {
                            rejected.push(`${file.name} (unsupported file type)`);
                        } else if (file.size > maxBytes) {
                            rejected.push(`${file.name} (over ${maxLabel})`);
                        } else if (total + file.size > maxTotalBytes) {
                            rejected.push(`${file.name} (would exceed the ${maxTotalLabel} total)`);
                        } else {
                            total += file.size;
                            valid.push(file);
                        }
                    }

                    this.error = rejected.length > 0
                        ? `Not added - ${rejected.join(', ')}. Allowed: ${allowedExtensions.join(', ')}, up to ${maxLabel} each and ${maxTotalLabel} in total.`
                        : '';
                    this.acceptedNames = valid.map(f => f.name);

                    // Native <input type=file>.files is read-only - rebuild it via
                    // DataTransfer so only the valid files actually get submitted.
                    const dt = new DataTransfer();
                    valid.forEach(f => dt.items.add(f));
                    this.$refs.fileInput.files = dt.files;
                }
            };
        }

        // wire:navigate can restore this page from its own cache when you
        // navigate back to it (e.g. after clicking Cancel then New Project
        // again) - that includes whatever the user had typed before leaving,
        // not just what the server originally rendered. Force a genuinely
        // blank form every time this page is actually the one being landed
        // on, since it's a "start fresh" form, not a draft to resume.
        document.addEventListener('livewire:navigated', () => {
            const form = document.getElementById('create-project-form');
            if (form) form.reset();
        });
    </script>
</x-app-layout>
