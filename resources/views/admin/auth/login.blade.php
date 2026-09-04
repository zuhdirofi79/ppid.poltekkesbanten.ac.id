<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin — {{ config('ppid.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-navy-dark via-navy to-green antialiased">
    <div class="mx-auto flex min-h-screen max-w-md items-center px-4 py-10">
        <div class="w-full rounded-2xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 text-center">
                <img
                    src="{{ asset(config('ppid.logo')) }}"
                    alt="Logo Poltekkes Kemenkes Banten"
                    width="56"
                    height="56"
                    class="mx-auto h-14 w-14 object-contain"
                >
                <h1 class="font-heading mt-3 text-xl font-bold text-navy">Login Admin PPID</h1>
                <p class="mt-1 text-sm text-muted">Poltekkes Kemenkes Banten</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login" class="mb-1 block text-sm font-medium text-navy">Email atau Username</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        value="{{ old('login') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-md border border-line px-3 py-2.5 text-sm focus:border-green focus:outline-none focus:ring-2 focus:ring-green/30"
                    >
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-navy">Kata sandi</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-md border border-line px-3 py-2.5 text-sm focus:border-green focus:outline-none focus:ring-2 focus:ring-green/30"
                    >
                </div>
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" value="1" class="rounded border-line text-green focus:ring-green">
                    Ingat saya
                </label>
                <button type="submit" class="font-heading w-full rounded-md bg-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-muted">
                <a href="{{ route('home') }}" class="text-green hover:underline">← Kembali ke situs publik</a>
            </p>
        </div>
    </div>
</body>
</html>
