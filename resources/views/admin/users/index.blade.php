@extends('admin.layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">Pengguna Admin</h1>
    <a href="{{ route('admin.users.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah pengguna</a>
</div>
@error('user')<p class="mb-4 text-sm text-red-600">{{ $message }}</p>@enderror
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Aktif</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->role?->label() }}</td>
                    <td class="px-4 py-3">{{ $user->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td class="px-4 py-3 text-right">
                        @if (! $admins->isProtectedAdmin($user))
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-green hover:underline">Edit</a>
                        @endif
                        @if ($user->id !== auth()->id() && ! $admins->isProtectedAdmin($user))
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus pengguna?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                        @elseif ($admins->isProtectedAdmin($user))
                            <span class="ml-3 text-xs text-muted">Akun terlindungi</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
