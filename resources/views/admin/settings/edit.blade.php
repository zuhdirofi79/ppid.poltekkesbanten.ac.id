@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">Pengaturan Situs</h1>
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
    @csrf @method('PUT')
    @forelse ($settings as $group => $items)
        <section class="rounded-xl border border-line bg-white p-5">
            <h2 class="font-heading mb-4 text-sm font-bold uppercase tracking-wide text-muted">{{ $group }}</h2>
            <div class="space-y-4">
                @foreach ($items as $setting)
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-navy" for="setting-{{ $setting->key }}">
                            {{ $setting->label ?? $setting->key }}
                        </label>
                        @if ($setting->type === 'textarea')
                            <textarea id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}]" rows="3" class="w-full rounded-md border border-line px-3 py-2 text-sm">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                        @else
                            <input id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}]" type="text" value="{{ old('settings.'.$setting->key, $setting->value) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
                        @endif
                        <p class="text-xs text-muted">Kunci: {{ $setting->key }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <p class="text-muted">Belum ada pengaturan. Jalankan seeder.</p>
    @endforelse
    @if ($settings->isNotEmpty())
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan pengaturan</button>
    @endif
</form>
@endsection
