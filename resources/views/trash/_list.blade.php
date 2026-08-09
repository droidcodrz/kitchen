  <div x-data="trashRestore()">

        @if($trashedProjects->isEmpty() && $trashedProducts->isEmpty() && $trashedCategories->isEmpty() && $trashedFolders->isEmpty())

            <div class="text-center py-12 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">

                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>

                </svg>

                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Waste bin is empty</h3>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deleted items will appear here.</p>

            </div>

        @else

            <!-- Projects Section -->

            @if($trashedProjects->isNotEmpty() && ($currentType === 'all' || $currentType === 'projects'))

                <div class="mb-8">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">

                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>

                        </svg>

                        Projects ({{ $trashedProjects->count() }})

                    </h2>

                    <div class="overflow-x-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">

                            <thead class="bg-gray-50 dark:bg-gray-800">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order No</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project Name</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deleted On</th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                                @foreach($trashedProjects as $project)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">

                                            {{ $project->order_no }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">

                                            {{ $project->name }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $project->client->name ?? 'N/A' }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $project->deleted_at->format('M d, Y H:i') }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            <button type="button" :disabled="restoring" @click="restore('project', {{ $project->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">

                                                Restore

                                            </button>

                                            <x-confirm-delete

                                                :action="route('trash.force-destroy', ['type' => 'project', 'id' => $project->id])"

                                                message="Are you sure you want to permanently delete this project? This action cannot be undone."

                                                title="Permanently Delete Project"

                                                buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"

                                                buttonText="Delete Forever">

                                            </x-confirm-delete>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

 

            <!-- Products Section -->

            @if($trashedProducts->isNotEmpty() && ($currentType === 'all' || $currentType === 'products'))

                <div class="mb-8">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">

                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>

                        </svg>

                        Products ({{ $trashedProducts->count() }})

                    </h2>

                    <div class="overflow-x-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">

                            <thead class="bg-gray-50 dark:bg-gray-800">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product Name</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Price</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deleted On</th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                                @foreach($trashedProducts as $product)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">

                                            {{ $product->sku }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">

                                            {{ $product->name }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $product->category->name ?? 'N/A' }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            ${{ number_format($product->unit_price, 2) }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $product->deleted_at->format('M d, Y H:i') }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            <button type="button" :disabled="restoring" @click="restore('product', {{ $product->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">

                                                Restore

                                            </button>

                                            <x-confirm-delete

                                                :action="route('trash.force-destroy', ['type' => 'product', 'id' => $product->id])"

                                                message="Are you sure you want to permanently delete this product? This action cannot be undone."

                                                title="Permanently Delete Product"

                                                buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"

                                                buttonText="Delete Forever">

                                            </x-confirm-delete>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

 

            <!-- Categories Section -->

            @if($trashedCategories->isNotEmpty() && ($currentType === 'all' || $currentType === 'categories'))

                <div class="mb-8">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">

                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>

                        </svg>

                        Categories ({{ $trashedCategories->count() }})

                    </h2>

                    <div class="overflow-x-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">

                            <thead class="bg-gray-50 dark:bg-gray-800">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category Name</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deleted On</th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                                @foreach($trashedCategories as $category)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">

                                            {{ $category->name }}

                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">

                                            {{ $category->description ?? 'N/A' }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $category->deleted_at->format('M d, Y H:i') }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            <button type="button" :disabled="restoring" @click="restore('category', {{ $category->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">

                                                Restore

                                            </button>

                                            <x-confirm-delete

                                                :action="route('trash.force-destroy', ['type' => 'category', 'id' => $category->id])"

                                                message="Are you sure you want to permanently delete this category? This action cannot be undone."

                                                title="Permanently Delete Category"

                                                buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"

                                                buttonText="Delete Forever">

                                            </x-confirm-delete>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

 

            <!-- Folders Section -->

            @if($trashedFolders->isNotEmpty() && ($currentType === 'all' || $currentType === 'folders'))

                <div class="mb-8">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">

                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>

                        </svg>

                        Product Folders ({{ $trashedFolders->count() }})

                    </h2>

                    <div class="overflow-x-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">

                            <thead class="bg-gray-50 dark:bg-gray-800">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Folder Name</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deleted On</th>

                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                                @foreach($trashedFolders as $folder)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">

                                            {{ $folder->name }}

                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">

                                            {{ $folder->description ?? 'N/A' }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $folder->products_count }} product(s)

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">

                                            {{ $folder->deleted_at->format('M d, Y H:i') }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            <button type="button" :disabled="restoring" @click="restore('folder', {{ $folder->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">

                                                Restore

                                            </button>

                                            <x-confirm-delete

                                                :action="route('trash.force-destroy', ['type' => 'folder', 'id' => $folder->id])"

                                                message="Are you sure you want to permanently delete this folder? This action cannot be undone."

                                                title="Permanently Delete Folder"

                                                buttonClass="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"

                                                buttonText="Delete Forever">

                                            </x-confirm-delete>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

        @endif

        </div>

 

        <script>

        function trashRestore() {

            return {

                restoring: false,

 

                async restore(type, id) {

                    this.restoring = true;

 

                    try {

                        const url = '{{ route('trash.restore', ['type' => '__TYPE__', 'id' => '__ID__']) }}'

                            .replace('__TYPE__', type)

                            .replace('__ID__', id);

                        const response = await fetch(url, {

                            method: 'POST',

                            headers: {

                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,

                            },

                        });

 

                        if (!response.ok) {

                            this.restoring = false;

                            return;

                        }

 

                        const listResponse = await fetch(window.location.href, {

                            headers: { 'X-Requested-With': 'XMLHttpRequest' },

                        });

                        const html = await listResponse.text();

                        document.getElementById('trash-list').innerHTML = html;

                    } catch (e) {

                        this.restoring = false;

                    }

                },

            };

        }

        </script>