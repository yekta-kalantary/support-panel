@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $label = method_exists($status, 'label') ? $status->label() : $value;
@endphp

<span class="badge badge-{{ str_replace('_', '-', $value) }}">{{ $label }}</span>
