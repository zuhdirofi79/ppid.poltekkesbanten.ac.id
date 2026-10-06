{{-- Dokumen DIK per tahun. Tahun baru berasal dari data, bukan cabang Blade. --}}
<section class="informasi-dikecualikan" @if ($excluded['selected']) aria-labelledby="informasi-dikecualikan-kicker" @endif>
    @if (count($excluded['years']) > 1)
        <nav class="mb-6" aria-label="Pilih Tahun">
            <p class="text-sm font-semibold text-navy">Pilih Tahun</p>
            <ul class="mt-2 flex flex-wrap gap-2">
                @foreach ($excluded['years'] as $year)
                    <li>
                        <a
                            href="{{ route('information.category', ['slug' => \App\Support\InformasiDikecualikan::SLUG, 'tahun' => $year]) }}"
                            class="inline-flex min-h-11 items-center rounded-full border px-4 py-2 text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold {{ (int) $excluded['selected'] === (int) $year ? 'border-navy bg-navy text-white' : 'border-line bg-white text-navy hover:border-navy' }}"
                            @if ((int) $excluded['selected'] === (int) $year) aria-current="page" @endif
                        >{{ $year }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif

    @if ($excluded['notice'])
        <p class="rounded-xl border border-dashed border-line bg-white p-6 text-sm text-muted">{{ $excluded['notice'] }}</p>
    @elseif ($excluded['selected'])
        <div class="ppid-request-section-intro">
            <h2 id="informasi-dikecualikan-kicker" class="ppid-request-section-title">
                {{ $excluded['heading_kicker'] }}
                <span class="mt-1 block">{{ $excluded['heading_year'] }}</span>
            </h2>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($excluded['cards'] as $card)
                <article class="ppid-request-receipt-card" aria-labelledby="dik-{{ $loop->index }}-title">
                    <div class="ppid-request-receipt-icon" aria-hidden="true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" />
                            <path d="M14 3v5h5" />
                            <path d="M8 13h8M8 17h5" />
                        </svg>
                    </div>

                    <div class="ppid-request-receipt-body">
                        <p class="ppid-request-receipt-meta">
                            @if ($card['available'])
                                <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                                <span>{{ $card['format'] }}</span>
                            @else
                                <span class="ppid-berkala-badge">{{ \App\Support\InformasiDikecualikan::MISSING_FILE }}</span>
                            @endif
                        </p>
                        <h3 id="dik-{{ $loop->index }}-title" class="ppid-request-receipt-title">{{ $card['title'] }}</h3>
                        @if ($card['description'])
                            <p class="ppid-request-receipt-note">{{ $card['description'] }}</p>
                        @endif
                    </div>

                    @if ($card['available'])
                        <div class="ppid-request-receipt-actions">
                            <a
                                href="{{ $card['href'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ppid-request-form-button"
                                aria-label="{{ $card['open_aria'] }}"
                            >Buka Dokumen</a>
                            <a
                                href="{{ $card['href'] }}"
                                class="ppid-request-secondary-action"
                                download="{{ $card['download_name'] }}"
                                aria-label="{{ $card['download_aria'] }}"
                            >Unduh Dokumen</a>
                        </div>
                    @endif
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-line bg-white p-6 text-sm text-muted">{{ \App\Support\InformasiDikecualikan::MISSING_FILE }}</p>
            @endforelse
        </div>
    @else
        <p class="rounded-xl border border-dashed border-line bg-white p-6 text-sm text-muted">{{ \App\Support\InformasiDikecualikan::MISSING_FILE }}</p>
    @endif
</section>
