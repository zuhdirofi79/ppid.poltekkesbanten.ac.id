@props(['label', 'name', 'value' => null, 'required' => false, 'rows' => 4])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-navy">
        {{ $label }}
        @if ($required)<span class="text-red-600">*</span>@endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        class="w-full rounded-md border border-line px-3 py-2 text-sm focus:border-green focus:outline-none focus:ring-2 focus:ring-green/20"
    >{{ old($name, $value) }}</textarea>
    @error($name)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
</div>
