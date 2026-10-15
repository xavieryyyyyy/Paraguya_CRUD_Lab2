@php
    // Generated component for session feedback used by scaffold auth views. The four customized
    // auth views display status directly instead.
@endphp
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
