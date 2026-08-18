@props(['messages'])

@php
    // $errors->get('field.*') returns messages grouped per index
    // (['field.0' => ['msg'], ...]), so the list can be nested. Flatten it -
    // rendering a nested array directly throws htmlspecialchars(): array given,
    // which turns any such validation failure into a 500 instead of a message.
    $flattened = \Illuminate\Support\Arr::flatten((array) ($messages ?? []));
@endphp

@if (count($flattened) > 0)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        @foreach ($flattened as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
