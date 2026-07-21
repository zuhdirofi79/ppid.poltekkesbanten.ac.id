@props(['label', 'name', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-navy">
        {{ $label }}
        @if ($required)<span class="text-red-600">*</span>@endif
    </label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        class="w-full rounded-md border border-line px-3 py-2 text-sm focus:border-green focus:outline-none focus:ring-2 focus:ring-green/20"
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) old($name, $value) === (string) $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
</div>
