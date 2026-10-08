@props(['class' => 'w-12 h-12'])

{{--
    The application's mark. Falls back to the built-in icon when no logo has
    been uploaded, so the login screen and the sidebar are never left with an
    empty space where the logo belongs.

    This reads the setting itself rather than taking it from the surrounding
    view: a Blade component has its own scope, so data handed to the layout by
    a view composer does not reach in here, and relying on it would have left
    the uploaded logo permanently invisible. The lookup is served from the same
    cached set of settings the rest of the page uses.

    object-contain rather than cover: a client's logo is rarely square, and
    cropping it to fit would cut parts of it off.
--}}
@php
    $logoUrl = \App\Models\AppSetting::logoUrl();
@endphp

@if($logoUrl)
    <img src="{{ $logoUrl }}"
         alt="{{ \App\Models\AppSetting::appName() }}"
         class="{{ $class }} object-contain">
@else
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
    </svg>
@endif
