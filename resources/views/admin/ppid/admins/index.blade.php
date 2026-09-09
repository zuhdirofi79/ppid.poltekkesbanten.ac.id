<!-- PPID-PROTECTED-ADMIN-MANAGEMENT-DEPLOY-20260909-A -->
@extends('admin.layouts.app')

@section('title', 'Manajemen Admin PPID')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-heading text-2xl font-bold text-navy">Manajemen Admin PPID</h1>
            <p class="mt-1 text-sm text-muted">Hanya Admin Inti yang dapat menambah atau menghapus Admin PPID tambahan.</p>
        </div>
        <a href="{{ route('admin.ppid.admins.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">Tambah Admin PPID</a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-line bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-line bg-surface text-xs uppercase tracking-wide text-muted">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Jenis Akun</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php $protected = $admins->isProtectedAdmin($user); @endphp
                    <tr class="border-b border-line/70">
                        <td class="px-4 py-3 font-medium text-navy">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role?->label() }}</td>
                        <td class="px-4 py-3">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="px-4 py-3">
                            @if ($protected)
                                <span class="inline-flex rounded-full bg-navy px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Admin Inti</span>
                            @else
                                <span class="text-muted">Admin PPID</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($protected)
                                <span class="text-xs font-medium text-muted">Akun terlindungi</span>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.ppid.admins.edit', $user) }}" class="text-xs font-semibold text-green hover:underline">Edit</a>
                                    <a href="{{ route('admin.ppid.admins.edit', $user) }}" class="text-xs font-semibold text-navy hover:underline">Reset Password</a>
                                    @if ($user->is_active)
                                        <form method="POST" action="{{ route('admin.ppid.admins.disable', $user) }}" class="inline" onsubmit="return confirm('Nonaktifkan admin ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs font-semibold text-amber-800 hover:underline">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.ppid.admins.enable', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs font-semibold text-green hover:underline">Aktifkan</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.ppid.admins.destroy', $user) }}" class="inline" onsubmit="return confirm('Hapus admin tambahan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-700 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
