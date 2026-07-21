<section class="bg-white py-14" aria-labelledby="maklumat-heading">
    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 lg:grid-cols-2">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-green">Profil</p>
            <h2 id="maklumat-heading" class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">
                Maklumat Pelayanan
            </h2>
            <p class="mt-3 text-muted">
                Komitmen Poltekkes Kemenkes Banten dalam memberikan pelayanan publik sesuai standar, transparan, akuntabel, dan berkelanjutan.
            </p>
            <a
                href="{{ route('profil.maklumat-pelayanan') }}"
                class="font-heading mt-6 inline-flex rounded-md bg-navy px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark"
            >
                Lihat Maklumat Pelayanan
            </a>
        </div>
        <a href="{{ route('profil.maklumat-pelayanan') }}" class="block overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition hover:border-green">
            <div class="aspect-[2/1] bg-surface">
                <img
                    src="{{ asset('images/ppid/maklumat-pelayanan-poltekkes-banten.webp') }}"
                    alt="Maklumat Pelayanan Poltekkes Kemenkes Banten"
                    width="1600"
                    height="800"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        </a>
    </div>
</section>
