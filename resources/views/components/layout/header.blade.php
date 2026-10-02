@php
    use Illuminate\Support\Carbon;

    $navigation = \App\Support\PublicNavigation::items();
    $contact = config('ppid.contact', []);
    $today = Carbon::now()->locale('id')->translatedFormat('l, d F Y');

    $resolveUrl = function (array $item): string {
        if (! empty($item['url'] ?? null)) {
            return $item['url'];
        }

        $route = $item['route'] ?? null;
        if (! $route) {
            return '#';
        }

        try {
            return route($route, $item['params'] ?? []);
        } catch (\Throwable) {
            return '#';
        }
    };

    $isChildActive = function (array $child): bool {
        $route = $child['route'] ?? null;
        if (! $route || ! request()->routeIs($route)) {
            return false;
        }

        foreach ($child['params'] ?? [] as $key => $value) {
            if ((string) request()->route($key) !== (string) $value) {
                return false;
            }
        }

        return true;
    };

    $isActive = function (array $item) use ($isChildActive): bool {
        if (($item['route'] ?? null) === 'home') {
            return request()->routeIs('home');
        }

        foreach ($item['children'] ?? [] as $child) {
            if ($isChildActive($child)) {
                return true;
            }
        }

        $route = $item['route'] ?? null;

        return $route ? request()->routeIs($route) : false;
    };
@endphp

<header
    class="site-header sticky top-0 z-50 bg-white transition-shadow"
    x-data="siteHeader"
    :class="{ 'is-scrolled': scrolled }"
>
    {{-- Top bar --}}
    <!-- PPID-ADMIN-LOGIN-NAV-DEPLOY-20260909-A -->
    <div class="bg-navy text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-2 text-xs sm:text-sm">
            <p class="truncate font-medium tracking-wide">
                <time datetime="{{ now()->toDateString() }}">{{ $today }}</time>
            </p>
            <div class="flex items-center gap-3 sm:gap-4">
                @if (! empty($contact['email']) && ! str_contains($contact['email'], 'TODO'))
                    <a href="mailto:{{ $contact['email'] }}" class="hidden hover:text-gold sm:inline">
                        {{ $contact['email'] }}
                    </a>
                @else
                    <a href="{{ route('contact') }}" class="hover:text-gold">Kontak PPID</a>
                @endif
                <x-search.global-search />
                @include('components.layout.admin-topbar')
            </div>
        </div>
    </div>

    {{-- Main navigation --}}
    <div class="border-b border-line bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                <img
                    src="{{ asset(config('ppid.logo')) }}"
                    alt="Logo {{ config('ppid.institution') }}"
                    width="48"
                    height="48"
                    class="h-11 w-11 shrink-0 object-contain sm:h-12 sm:w-12"
                >
                <span class="min-w-0">
                    <span class="font-heading block text-sm font-bold leading-tight text-navy sm:text-base">PPID</span>
                    <span class="block text-[11px] leading-snug text-muted sm:text-xs">Poltekkes Kemenkes Banten</span>
                </span>
            </a>

            <button
                type="button"
                class="inline-flex items-center justify-center rounded-md border border-line p-2 text-navy lg:hidden"
                @click="mobileOpen = !mobileOpen"
                :aria-expanded="mobileOpen.toString()"
                aria-controls="mobile-navigation"
                aria-label="Buka menu navigasi"
            >
                <svg x-show="!mobileOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"></path>
                </svg>
                <svg x-cloak x-show="mobileOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"></path>
                </svg>
            </button>

            <nav class="hidden lg:block" aria-label="Navigasi utama">
                <!-- PPID-SOP-LAYANAN-NAV-DEPLOY-20260916-A -->
                <ul class="flex items-center gap-1">
                    @foreach ($navigation as $index => $item)
                        @php
                            $key = 'd'.$index;
                            $panelId = 'nav-dropdown-'.$key;
                            $hasChildren = ! empty($item['children']);
                            $active = $isActive($item);
                            $href = $resolveUrl($item);
                            $wideMenu = collect($item['children'] ?? [])->contains(
                                fn (array $child) => mb_strlen((string) ($child['label'] ?? '')) > 42
                            );
                        @endphp
                        <li
                            class="relative"
                            @mouseenter="openDesktop = '{{ $key }}'"
                            @mouseleave="closeDesktop()"
                            @focusin="openDesktop = '{{ $key }}'"
                        >
                            @if ($hasChildren)
                                <button
                                    type="button"
                                    class="font-heading inline-flex items-center gap-1 whitespace-nowrap rounded-md px-3 py-2 text-sm font-semibold transition {{ $active ? 'text-green' : 'text-navy hover:text-green' }}"
                                    @click="toggleDesktop('{{ $key }}')"
                                    @keydown.escape="closeDesktop()"
                                    :aria-expanded="(openDesktop === '{{ $key }}').toString()"
                                    aria-haspopup="true"
                                    aria-controls="{{ $panelId }}"
                                    @if ($active) aria-current="page" @endif
                                >
                                    {{ $item['label'] }}
                                    <svg class="h-3.5 w-3.5 transition" :class="openDesktop === '{{ $key }}' ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M5.25 7.5 10 12.25 14.75 7.5" />
                                    </svg>
                                </button>
                                <div
                                    id="{{ $panelId }}"
                                    x-cloak
                                    x-show="openDesktop === '{{ $key }}'"
                                    x-transition.opacity.duration.150ms
                                    class="nav-dropdown-panel absolute left-0 z-50 min-w-[16rem]"
                                    @keydown.escape="closeDesktop()"
                                >
                                    <ul class="rounded-lg border border-line bg-white py-2 shadow-lg" role="menu" @if ($wideMenu) style="width: min(22rem, calc(100vw - 2rem))" @endif>
                                        @foreach ($item['children'] as $child)
                                            <li role="none">
                                                <a
                                                    role="menuitem"
                                                    href="{{ $resolveUrl($child) }}"
                                                    class="block px-4 py-2 text-sm text-ink hover:bg-surface hover:text-green"
                                                >
                                                    {{ $child['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <a
                                    href="{{ $href }}"
                                    class="font-heading inline-flex items-center whitespace-nowrap rounded-md px-3 py-2 text-sm font-semibold transition {{ $active ? 'text-green' : 'text-navy hover:text-green' }}"
                                    @if ($active) aria-current="page" @endif
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>

    {{-- Mobile accordion --}}
    <div
        id="mobile-navigation"
        x-cloak
        x-show="mobileOpen"
        x-transition
        class="border-b border-line bg-white lg:hidden"
    >
        <nav class="mx-auto max-w-7xl px-4 py-3" aria-label="Navigasi seluler">
            <ul class="space-y-1">
                @foreach ($navigation as $index => $item)
                    @php
                        $key = 'm'.$index;
                        $panelId = 'nav-mobile-'.$key;
                        $hasChildren = ! empty($item['children']);
                        $active = $isActive($item);
                    @endphp
                    <li class="border-b border-line/70 last:border-0">
                        @if ($hasChildren)
                            <button
                                type="button"
                                class="font-heading flex w-full items-center justify-between py-3 text-left text-sm font-semibold {{ $active ? 'text-green' : 'text-navy' }}"
                                @click="toggleMobile('{{ $key }}')"
                                :aria-expanded="(openMobile === '{{ $key }}').toString()"
                                aria-haspopup="true"
                                aria-controls="{{ $panelId }}"
                                @if ($active) aria-current="page" @endif
                            >
                                <span>{{ $item['label'] }}</span>
                                <svg class="h-4 w-4 transition" :class="openMobile === '{{ $key }}' ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M5.25 7.5 10 12.25 14.75 7.5" />
                                </svg>
                            </button>
                            <ul id="{{ $panelId }}" x-cloak x-show="openMobile === '{{ $key }}'" x-transition class="space-y-1 pb-3 pl-3">
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <a
                                            href="{{ $resolveUrl($child) }}"
                                            class="block rounded-md px-2 py-2 text-sm text-muted hover:bg-surface hover:text-green"
                                            @click="closeMobileMenu()"
                                        >
                                            {{ $child['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a
                                href="{{ $resolveUrl($item) }}"
                                class="font-heading block py-3 text-sm font-semibold {{ $active ? 'text-green' : 'text-navy' }}"
                                @click="closeMobileMenu()"
                                @if ($active) aria-current="page" @endif
                            >
                                {{ $item['label'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
                @if ($ppidAdminChrome['showLogin'] ?? true)
                    <li class="border-b border-line/70 last:border-0">
                        <a
                            href="{{ route('admin.login') }}"
                            class="font-heading block py-3 text-sm font-semibold text-navy"
                            @click="closeMobileMenu()"
                        >
                            Login Admin
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>
