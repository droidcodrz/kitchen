@props(['value', 'options'])

{{--
    Keeps a record's saved value visible when it is not in the dropdown list.

    Values were stored before these lists existed, and some of them no longer
    appear in the options. Without this the select falls back to its blank
    placeholder, so the saved value looks lost - and on a required field the
    form cannot be submitted at all until the user re-picks something, which
    silently discards whatever was really there.

    Listing it as its own option shows what the record actually holds and lets
    the form save unchanged. Marked so it is clear the value is not one of the
    configured choices.
--}}
@php
    $current = is_null($value) ? '' : (string) $value;
    $known = collect($options ?? [])->contains(fn ($option) => (string) $option->value === $current);
@endphp

@if($current !== '' && !$known)
    <option value="{{ $current }}" selected>{{ $current }} (not in list)</option>
@endif
