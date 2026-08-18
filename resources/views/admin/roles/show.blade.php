<x-app-layout>
    <x-slot name="title">Role Details</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Role: {{ $role->name }}</h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Permissions ({{ $role->permissions->count() }})</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($role->permissions as $permission)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ $permission->name }}</span>
                @endforeach
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Users ({{ $role->users->count() }})</h3>
            <ul class="space-y-2">
                @forelse ($role->users as $user)
                    <li class="text-sm text-gray-700 dark:text-gray-300">{{ $user->full_name }} ({{ $user->email }})</li>
                @empty
                    <li class="text-sm text-gray-500">No users with this role</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
