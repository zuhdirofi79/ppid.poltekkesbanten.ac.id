<!-- PPID-ADMIN-LOGIN-NAV-DEPLOY-20260909-A -->
@php
    $chrome = $ppidAdminChrome ?? [
        'showLogin' => true,
        'showChrome' => false,
        'showBell' => false,
        'isProtected' => false,
        'missingCount' => 0,
        'badgeLabel' => '',
        'missingDocuments' => collect(),
        'missingLhkpn' => collect(),
        'lhkpnMissingCount' => 0,
        'notificationTotal' => 0,
    ];
    $user = auth()->user();
    $bellCount = (int) ($chrome['notificationTotal'] ?? $chrome['missingCount'] ?? 0);
    $bellLabel = $bellCount > 99 ? '99+' : ($bellCount > 0 ? (string) $bellCount : '');
    $missingLhkpn = $chrome['missingLhkpn'] ?? collect();
@endphp

@if ($chrome['showLogin'])
    <a
        href="{{ route('admin.login') }}"
        class="inline-flex min-h-11 min-w-11 items-center justify-center gap-1 rounded-md px-2 py-1 text-white hover:bg-white/10 hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold"
        aria-label="Login Admin"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="5" y="11" width="14" height="10" rx="2"></rect>
            <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
        </svg>
        <span class="hidden sm:inline">Login Admin</span>
    </a>
@endif

@if ($chrome['showBell'])
    <!-- PPID-ADMIN-NOTIFICATION-DEPLOY-20260909-A -->
    <div class="relative" @click.outside="bellOpen = false">
        <button
            type="button"
            class="relative inline-flex min-h-11 min-w-11 items-center justify-center rounded-md px-2 py-1 text-white hover:bg-white/10 hover:text-gold"
            @click="toggleBell()"
            @keydown.escape="bellOpen = false"
            aria-label="Notifikasi dokumen PPID"
            :aria-expanded="bellOpen.toString()"
            aria-controls="ppid-admin-notification-dropdown"
            aria-haspopup="true"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 01-3.46 0"></path>
            </svg>
            @if ($bellLabel !== '')
                <span
                    class="absolute -right-0.5 -top-0.5 inline-flex min-w-[1.15rem] items-center justify-center rounded-full bg-amber-400 px-1 text-[10px] font-bold leading-4 text-navy-dark"
                    aria-label="{{ $bellCount }} dokumen belum lengkap"
                >{{ $bellLabel }}</span>
            @endif
        </button>

        <div
            id="ppid-admin-notification-dropdown"
            x-cloak
            x-show="bellOpen"
            x-transition.opacity.duration.150ms
            class="ppid-admin-dropdown absolute right-0 z-50 mt-2 w-[22rem] overflow-hidden rounded-xl border border-line bg-white text-ink shadow-lg"
            role="menu"
            @keydown.escape="bellOpen = false"
        >
            <div class="border-b border-line px-4 py-3">
                <p class="font-heading text-sm font-bold text-navy">Dokumen Wajib Dilengkapi</p>
                @if ($chrome['missingCount'] > 0)
                    <p class="mt-1 text-xs text-muted">
                        Masih terdapat {{ $chrome['missingCount'] }} dokumen Informasi Berkala yang belum tersedia.
                    </p>
                @endif
                @if ($missingLhkpn->isNotEmpty())
                    <p class="mt-1 text-xs text-muted">
                        {{ $missingLhkpn->count() }} dokumen LHKPN belum diunggah.
                    </p>
                @endif
            </div>

            @if ($bellCount === 0)
                <p class="px-4 py-6 text-sm text-green">✓ Seluruh dokumen Informasi Berkala telah tersedia.</p>
            @else
                <ul class="max-h-80 divide-y divide-line overflow-y-auto">
                    @foreach ($chrome['missingDocuments'] as $doc)
                        <li>
                            <a
                                role="menuitem"
                                href="{{ route('admin.documents.edit', $doc) }}"
                                class="flex gap-3 px-4 py-3 hover:bg-surface"
                            >
                                <span class="mt-0.5 text-navy" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"></path>
                                        <path d="M15 3v5h5"></path>
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-medium text-navy">{{ $doc->title }}</span>
                                    <span class="mt-0.5 block text-xs text-muted">{{ $doc->section_title ?: 'Informasi Berkala' }}</span>
                                    <span class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-900">Wajib dilengkapi</span>
                                    <span class="mt-1 block text-xs font-semibold text-green">Upload Dokumen →</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                    @foreach ($missingLhkpn as $lhkpn)
                        <li>
                            <a
                                role="menuitem"
                                href="{{ route('admin.lhkpn.edit', $lhkpn) }}"
                                class="flex gap-3 px-4 py-3 hover:bg-surface"
                            >
                                <span class="mt-0.5 text-navy" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"></path>
                                        <path d="M15 3v5h5"></path>
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-medium text-navy">{{ $lhkpn->person_name }}</span>
                                    <span class="mt-0.5 block text-xs text-muted">{{ $lhkpn->position }}</span>
                                    <span class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-900">Wajib dilengkapi</span>
                                    <span class="mt-1 block text-xs font-semibold text-green">Upload Dokumen →</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-line px-4 py-3 space-y-2">
                    @if ($chrome['missingCount'] > 0)
                        <a href="{{ route('admin.informasi-publik.berkala', ['status' => 'missing']) }}" class="block text-sm font-semibold text-green hover:underline">
                            Lihat Semua Dokumen Belum Lengkap
                        </a>
                    @endif
                    @if ($missingLhkpn->isNotEmpty())
                        <a href="{{ route('admin.lhkpn.index') }}" class="block text-sm font-semibold text-green hover:underline">
                            Lengkapi LHKPN Pimpinan
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif

@if ($chrome['showChrome'] && $user)
    <div class="relative" @click.outside="profileOpen = false">
        <button
            type="button"
            class="inline-flex min-h-11 items-center gap-2 rounded-md px-2 py-1 text-white hover:bg-white/10 hover:text-gold"
            @click="toggleProfile()"
            @keydown.escape="profileOpen = false"
            :aria-expanded="profileOpen.toString()"
            aria-haspopup="true"
            aria-controls="ppid-admin-profile-dropdown"
            aria-label="Menu akun {{ $user->name }}"
        >
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/15 text-[11px] font-bold" aria-hidden="true">{{ $user->initials() }}</span>
            <span class="hidden max-w-[9rem] truncate sm:inline">{{ $user->name }}</span>
            <svg class="hidden h-3.5 w-3.5 sm:inline" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5" /></svg>
        </button>
        <div
            id="ppid-admin-profile-dropdown"
            x-cloak
            x-show="profileOpen"
            x-transition.opacity.duration.150ms
            class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-xl border border-line bg-white py-2 text-ink shadow-lg"
            role="menu"
        >
            @if ($user->hasRole(\App\Enums\UserRole::AdminPpid))
                <a role="menuitem" href="{{ route('admin.ppid.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-surface">Dashboard Admin PPID</a>
            @else
                <a role="menuitem" href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-surface">Dashboard</a>
            @endif
            @can('manage-documents')
                <a role="menuitem" href="{{ route('admin.informasi-publik.berkala') }}" class="block px-4 py-2 text-sm hover:bg-surface">Dokumen Informasi Publik</a>
                <a role="menuitem" href="{{ route('admin.informasi-publik.berkala', ['status' => 'missing']) }}" class="block px-4 py-2 text-sm hover:bg-surface">Dokumen Wajib Dilengkapi</a>
            @endcan
            <a role="menuitem" href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-surface">Profil Saya</a>
            @if ($chrome['isProtected'])
                {{-- PPID-PROTECTED-ADMIN-MANAGEMENT-DEPLOY-20260909-A --}}
                <a role="menuitem" href="{{ route('admin.ppid.admins.index') }}" class="block px-4 py-2 text-sm hover:bg-surface">Manajemen Admin PPID</a>
            @endif
            <form method="POST" action="{{ route('admin.logout') }}" class="border-t border-line">
                @csrf
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-700 hover:bg-surface" role="menuitem">Logout</button>
            </form>
        </div>
    </div>
@endif
