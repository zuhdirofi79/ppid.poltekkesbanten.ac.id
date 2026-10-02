@extends('admin.layouts.app')
@section('title', 'Profil Pimpinan')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">Profil Pimpinan</h1>
        <p class="text-sm text-muted">Perubahan di sini menjadi isi halaman publik. Seeder tidak menimpa suntingan ini.</p>
    </div>
    <a href="{{ route('admin.leadership-profiles.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">+ Tambah Profil Pimpinan</a>
</div>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Jabatan</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Urutan</th>
                <th class="px-4 py-3">Diperbarui</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($profiles as $profile)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $profile->displayName() }}</td>
                    <td class="px-4 py-3">{{ $profile->position }}</td>
                    <td class="px-4 py-3">{{ $profile->is_published ? 'Tayang' : 'Disembunyikan' }}</td>
                    <td class="px-4 py-3">{{ $profile->sort_order }}</td>
                    <td class="px-4 py-3 text-muted">{{ optional($profile->updated_at)->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.leadership-profiles.edit', $profile) }}" class="text-navy">Edit</a>
                        <a href="{{ route('pages.show', 'profil-pimpinan-satuan-kerja') }}" class="ml-3 text-navy" target="_blank" rel="noopener">Preview</a>
                        <form method="POST" action="{{ route('admin.leadership-profiles.publish', $profile) }}" class="inline">
                            @csrf
                            <button class="ml-3 text-navy">{{ $profile->is_published ? 'Sembunyikan' : 'Tayangkan' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.leadership-profiles.destroy', $profile) }}" class="inline" onsubmit="return confirm('Hapus profil ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="ml-3 text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-muted">Belum ada profil. Jalankan LeadershipProfileSeeder atau tambah dari tombol di atas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
