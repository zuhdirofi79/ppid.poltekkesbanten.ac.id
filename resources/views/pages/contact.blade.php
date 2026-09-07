@extends('layouts.app')

@section('title', $title.' — '.config('ppid.name'))
@section('meta_description', $description ?? config('ppid.name_long'))

@section('content')
    @php $contact = config('ppid.contact', []); @endphp

    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-navy" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
            <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">{{ $title }}</h1>
            <p class="mt-2 max-w-3xl text-muted">{{ $description }}</p>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 lg:grid-cols-2">
        <div class="rounded-xl border border-line p-6">
            <h2 class="font-heading text-lg font-bold text-navy">Informasi kontak</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @if (! empty($contact['address']))
                    <li><span class="text-muted">Alamat:</span> {{ $contact['address'] }}</li>
                @endif
                @if (! empty($contact['email']))
                    <li>
                        <span class="text-muted">Email:</span>
                        <a href="mailto:{{ $contact['email'] }}" class="text-green hover:underline">{{ $contact['email'] }}</a>
                    </li>
                @endif
                @if (! empty($contact['whatsapp']))
                    <li>
                        <span class="text-muted">WhatsApp/Telepon Humas:</span>
                        @if (! empty($contact['whatsapp_url']))
                            <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="text-green hover:underline">{{ $contact['whatsapp'] }}</a>
                        @else
                            {{ $contact['whatsapp'] }}
                        @endif
                    </li>
                @endif
                @if (! empty($contact['service_hours']))
                    <li><span class="text-muted">Jam layanan:</span> {{ $contact['service_hours'] }}</li>
                @endif
            </ul>
        </div>
        <div class="rounded-xl border border-line bg-surface/60 p-6">
            <h2 class="font-heading text-lg font-bold text-navy">Lokasi kampus</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach (config('ppid.campuses', []) as $campus)
                    <li>
                        <p class="font-semibold text-navy">{{ $campus['name'] }}</p>
                        <p class="text-muted">{{ $campus['address'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
