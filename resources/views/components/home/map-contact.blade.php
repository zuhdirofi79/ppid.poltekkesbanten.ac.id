@php
    $contact = config('ppid.contact', []);
    $map = config('ppid.map', []);
    $campuses = config('ppid.campuses', []);
@endphp

<section class="bg-white py-14" aria-labelledby="peta-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-8 max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-wider text-green">Lokasi & kontak</p>
            <h2 id="peta-heading" class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">Peta dan Kontak</h2>
            <p class="mt-3 text-muted">
                Hubungi PPID Poltekkes Kemenkes Banten atau kunjungi lokasi kampus.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-line bg-surface">
                @if (! empty($map['embed_url']))
                    <iframe
                        title="{{ $map['title'] ?? 'Peta lokasi' }}"
                        src="{{ $map['embed_url'] }}"
                        class="h-72 w-full border-0 lg:h-full min-h-[18rem]"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                @else
                    <div class="flex h-72 flex-col items-center justify-center gap-4 bg-gradient-to-br from-navy to-green p-6 text-center text-white lg:min-h-[22rem]">
                        <svg class="h-12 w-12 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" />
                            <circle cx="12" cy="10" r="2.5" />
                        </svg>
                        <div>
                            <p class="font-heading text-lg font-bold">{{ $map['title'] ?? 'Lokasi' }}</p>
                            <p class="mt-2 text-sm text-white/80">
                                Embed peta belum dikonfigurasi. Gunakan tautan peta publik atau isi
                                <code class="rounded bg-white/15 px-1">ppid.map.embed_url</code>.
                            </p>
                        </div>
                        @if (! empty($map['external_url']))
                            <a
                                href="{{ $map['external_url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-heading rounded-md bg-white px-4 py-2 text-sm font-semibold text-navy hover:bg-gold"
                            >
                                Buka peta eksternal
                                <span class="sr-only">(membuka tab baru)</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <h3 class="font-heading text-lg font-bold text-navy">Kontak PPID</h3>
                <ul class="mt-4 space-y-3 text-sm text-ink">
                    @if (! empty($contact['address']))
                        <li>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-muted">Alamat</span>
                            {{ $contact['address'] }}
                        </li>
                    @endif
                    @if (! empty($contact['email']))
                        <li>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-muted">Email</span>
                            <a href="mailto:{{ $contact['email'] }}" class="text-green hover:underline">{{ $contact['email'] }}</a>
                        </li>
                    @endif
                    @if (! empty($contact['whatsapp']))
                        <li>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-muted">WhatsApp/Telepon Humas</span>
                            @if (! empty($contact['whatsapp_url']))
                                <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="text-green hover:underline">{{ $contact['whatsapp'] }}</a>
                            @else
                                {{ $contact['whatsapp'] }}
                            @endif
                        </li>
                    @endif
                    @if (! empty($contact['service_hours']))
                        <li>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-muted">Jam layanan</span>
                            {{ $contact['service_hours'] }}
                        </li>
                    @endif
                </ul>

                <h3 class="font-heading mt-8 text-lg font-bold text-navy">Lokasi Kampus</h3>
                <ul class="mt-3 space-y-3 text-sm">
                    @foreach ($campuses as $campus)
                        <li class="rounded-lg border border-line bg-surface/60 px-4 py-3">
                            <p class="font-semibold text-navy">{{ $campus['name'] }}</p>
                            <p class="text-muted">{{ $campus['address'] }}</p>
                        </li>
                    @endforeach
                </ul>

                <a
                    href="{{ route('contact') }}"
                    class="font-heading mt-6 inline-flex rounded-md bg-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-light"
                >
                    Halaman kontak lengkap
                </a>
            </div>
        </div>
    </div>
</section>
