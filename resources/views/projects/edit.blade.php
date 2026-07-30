<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('projects.show', $project) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Project') }}: {{ $project->name }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg">
        <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PATCH')

            {{-- Basic Info --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" :value="__('Project Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="client_id" :value="__('Client')" />
                        <select id="client_id" name="client_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                    </div>

                    @if(in_array($project->status, ['draft', 'confirmed']))
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(['draft','confirmed'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    @else
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <div class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md shadow-sm">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                @if($project->status === 'delayed' && $project->delivery_date && $project->delivery_date->isPast())
                                    <span class="ml-2 text-xs text-red-600 dark:text-red-400">(Auto-marked: delivery date passed)</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Use "Change Status" button on project details page to move to next stage.
                                @if(in_array($project->status, ['confirmed', 'in_production']))
                                    <br><span class="text-red-500">Note: Will auto-mark as "Delayed" in real-time if delivery date passes.</span>
                                @endif
                            </p>
                            <input type="hidden" name="status" value="{{ $project->status }}">
                        </div>
                    @endif

                    <div>
                        <x-input-label for="project_manager_id" :value="__('Project Manager')" />
                        <select id="project_manager_id" name="project_manager_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Manager</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('project_manager_id')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Equipment / Products --}}
            @php
                $existingProducts = old('products', $project->products->map(fn($p) => ['product_id' => $p->id, 'quantity' => $p->pivot->quantity])->toArray());
                if (empty($existingProducts)) $existingProducts = [['product_id' => '', 'quantity' => 1]];
            @endphp
            <div x-data="{
                products: {{ json_encode($existingProducts) }},
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

            {{-- Timeline --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Timeline</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="proposal_signed_date" :value="__('Proposal Signed Date')" />
                        <x-text-input id="proposal_signed_date" name="proposal_signed_date" type="date" class="mt-1 block w-full" :value="old('proposal_signed_date', $project->proposal_signed_date?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('proposal_signed_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="delivery_date" :value="__('Delivery Date')" />
                        <x-text-input id="delivery_date" name="delivery_date" type="date" class="mt-1 block w-full" :value="old('delivery_date', $project->delivery_date?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('delivery_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="production_deadline" :value="__('Production Deadline')" />
                        <x-text-input id="production_deadline" name="production_deadline" type="date" class="mt-1 block w-full" :value="old('production_deadline', $project->production_deadline?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('production_deadline')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Existing Attachments --}}
            @if($project->attachments->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Current Attachments</h3>
                    <div class="space-y-2">
                        @foreach($project->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment->original_name ?? $attachment->file_name ?? 'Attachment' }}</span>
                                </div>
                                <label class="flex items-center text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" class="rounded border-gray-300 dark:border-gray-600 text-red-600 shadow-sm focus:ring-red-500 mr-1" />
                                    Remove
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- New Attachments --}}
            <div x-data="{ selectedFiles: [] }">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Attachments</h3>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                <span>Upload files</span>
                                <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple @change="selectedFiles = Array.from($event.target.files)" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, PNG, JPG, DWG up to 10MB each</p>
                    </div>
                </div>
                <template x-if="selectedFiles.length > 0">
                    <ul class="mt-3 space-y-1">
                        <template x-for="file in selectedFiles" :key="file.name + file.size">
                            <li class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span x-text="file.name" class="truncate"></span>
                            </li>
                        </template>
                    </ul>
                </template>
                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
            </div>

            {{-- Team Members --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Team Members</h3>
                @php
                    $currentMembers = old('members', $project->members->pluck('id')->toArray());
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($users ?? [] as $user)
                        <label class="relative flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($user->id, $currentMembers) ? 'checked' : '' }} />
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
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $project->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <x-primary-button>
                    Update Project
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
