<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin PPID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-ink antialiased" x-data="{ mobileNav: false }">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 bg-navy-dark text-white lg:block">
            @include('admin.partials.nav')
        </aside>

        <div
            x-cloak
            x-show="mobileNav"
            class="fixed inset-0 z-50 bg-navy-dark/95 p-4 text-white lg:hidden"
        >
            <div class="mb-4 flex justify-end">
                <button type="button" class="rounded border border-white/30 px-3 py-1 text-sm" @click="mobileNav = false">Tutup</button>
            </div>
            @include('admin.partials.nav')
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between gap-3 border-b border-line bg-white px-4 py-3 sm:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded border border-line px-2 py-1 text-sm lg:hidden" @click="mobileNav = true" aria-label="Buka menu">☰</button>
                    <div>
                        <p class="font-heading text-sm font-bold text-navy lg:hidden">Admin PPID</p>
                        <p class="text-xs text-muted">{{ auth()->user()?->role?->label() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="hidden text-muted sm:inline">{{ auth()->user()?->name }}</span>
                    <a href="{{ route('home') }}" class="text-green hover:underline" target="_blank" rel="noopener noreferrer">Lihat situs</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-line px-3 py-1.5 font-medium text-navy hover:border-green">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-green/30 bg-green/10 px-4 py-3 text-sm text-green">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
