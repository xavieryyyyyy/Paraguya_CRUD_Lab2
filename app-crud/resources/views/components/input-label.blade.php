@php
    // Reusable label component: receives a value and forwards HTML attributes. Native labels
    // are used in the four custom auth forms.
@endphp
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>
