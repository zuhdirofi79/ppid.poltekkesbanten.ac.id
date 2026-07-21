@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])

@php
    $fieldValue = old($name, $value);
@endphp

<div {{ $attributes->class(['space-y-1']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-navy">
        {{ $label }}
        @if ($required)<span class="text-red-600">*</span>@endif
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $fieldValue }}"
        @if ($required) required @endif
        class="w-full rounded-md border border-line px-3 py-2 text-sm focus:border-green focus:outline-none focus:ring-2 focus:ring-green/20"
    >
    @if ($help)
        <p class="text-xs text-muted">{{ $help }}</p>
    @endif
    @error($name)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
</div>
