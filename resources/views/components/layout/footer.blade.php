@php
    $contact = config('ppid.contact', []);
    $campuses = config('ppid.campuses', []);
    $links = config('ppid.important_links', []);
@endphp

<footer class="mt-auto bg-navy-dark text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="mb-4 flex items-center gap-3">
                <img
                    src="{{ asset(config('ppid.logo')) }}"
                    alt="Logo {{ config('ppid.institution') }}"
                    width="48"
                    height="48"
                    class="h-12 w-12 rounded bg-white object-contain p-1"
                >
                <div>
                    <p class="font-heading text-base font-bold leading-tight">PPID</p>
                    <p class="text-xs text-white/80">Poltekkes Kemenkes Banten</p>
                </div>
            </div>
            <p class="mb-4 text-sm leading-relaxed text-white/80">
                {{ config('ppid.name_long') }}
            </p>
            <!-- PPID-FOOTER-CONTACT-DEPLOY-20260907-A -->
            <ul class="space-y-2 text-sm text-white/85">
                @if (! empty($contact['website']))
                    <li>
                        <span class="text-white/60">Website resmi:</span>
                        <a
                            href="{{ $contact['website'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 break-words hover:text-gold"
                        >
                            {{ $contact['website_label'] ?? 'poltekkesbanten.ac.id' }}
                            <span class="sr-only">(membuka tab baru)</span>
                            <svg class="h-3.5 w-3.5 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M14 4h6v6M10 14 20 4M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5" />
                            </svg>
                        </a>
                    </li>
                @endif
                @if (! empty($contact['email']))
                    <li>
                        <span class="text-white/60">Email:</span>
                        <a href="mailto:{{ $contact['email'] }}" class="break-all hover:text-gold">
                            {{ $contact['email'] }}
                        </a>
                    </li>
                @endif
                @if (! empty($contact['whatsapp_url']) && ! empty($contact['whatsapp']))
                    <li>
                        <span class="text-white/60">WhatsApp/Telepon Humas:</span>
                        <a
                            href="{{ $contact['whatsapp_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 break-words hover:text-gold"
                        >
                            {{ $contact['whatsapp'] }}
                            <span class="sr-only">(membuka tab baru)</span>
                        </a>
                    </li>
                @endif
                @if (! empty($contact['service_hours']))
                    <li>
                        <span class="text-white/60">Jam layanan:</span>
                        {{ $contact['service_hours'] }}
                    </li>
                @endif
            </ul>
        </div>

        <div>
            <h2 class="font-heading mb-4 text-sm font-bold uppercase tracking-wider text-gold">Lokasi Kampus</h2>
            <ul class="space-y-3 text-sm text-white/85">
                @foreach ($campuses as $campus)
                    <li>
                        <p class="font-semibold text-white">{{ $campus['name'] }}</p>
                        <p class="text-white/70">{{ $campus['address'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-heading mb-4 text-sm font-bold uppercase tracking-wider text-gold">Tautan Penting</h2>
            <ul class="space-y-2 text-sm">
                @foreach ($links as $link)
                    <li>
                        <a
                            href="{{ $link['url'] }}"
                            class="inline-flex items-center gap-1 text-white/85 hover:text-gold"
                            @if (! empty($link['external']))
                                target="_blank"
                                rel="noopener noreferrer"
                            @endif
                        >
                            {{ $link['label'] }}
                            @if (! empty($link['external']))
                                <span class="sr-only">(membuka tab baru)</span>
                                <svg class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M14 4h6v6M10 14 20 4M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5" />
                                </svg>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-heading mb-4 text-sm font-bold uppercase tracking-wider text-gold">Menu Cepat</h2>
            <ul class="space-y-2 text-sm text-white/85">
                <li><a class="hover:text-gold" href="{{ route('information.index') }}">Informasi Publik</a></li>
                <li><a class="hover:text-gold" href="{{ route('documents.index') }}">Dokumen Publik</a></li>
                <li><a class="hover:text-gold" href="{{ route('services.show', ['slug' => 'permohonan-informasi']) }}">Permohonan Informasi</a></li>
                <li><a class="hover:text-gold" href="{{ route('faqs.index') }}">FAQ</a></li>
                <li><a class="hover:text-gold" href="{{ route('contact') }}">Kontak</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-white/65 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ config('ppid.name') }}. Hak cipta dilindungi.</p>
            <p>Dikembangkan untuk transparansi informasi publik.</p>
        </div>
    </div>
</footer>
