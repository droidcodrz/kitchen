<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Too Large - {{ config('app.name', 'Kitchen') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-8 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
            </div>

            <h1 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Upload too large</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $message }}</p>

            <a href="{{ $backUrl }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Go back and try again
            </a>
        </div>
    </div>
</body>
</html>
