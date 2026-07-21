@props(['checked' => false, 'name', 'label'])

<label class="inline-flex items-center gap-2 text-sm text-ink">
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        class="rounded border-line text-green focus:ring-green"
        @checked(old($name, $checked))
    >
    {{ $label }}
</label>
