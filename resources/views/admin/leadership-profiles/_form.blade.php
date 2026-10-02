@csrf
<label class="block text-sm">Nama
    <input name="name" value="{{ old('name', $profile->name) }}" required class="mt-1 w-full rounded-md border border-line px-3 py-2">
</label>
<label class="block text-sm">Gelar
    <input name="credentials" value="{{ old('credentials', $profile->credentials) }}" class="mt-1 w-full rounded-md border border-line px-3 py-2">
    <span class="text-xs text-muted">Kosongkan bila gelar sudah tertulis di nama.</span>
</label>
<label class="block text-sm">Jabatan
    <input name="position" value="{{ old('position', $profile->position) }}" required class="mt-1 w-full rounded-md border border-line px-3 py-2">
</label>
<label class="block text-sm">Foto PNG, JPG, atau WebP, maksimal 5 MB
    <input type="file" name="photo" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" class="mt-1 block w-full text-sm">
</label>
@error('photo')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
<label class="block text-sm">Ringkasan
    <textarea name="short_summary" rows="4" class="mt-1 w-full rounded-md border border-line px-3 py-2">{{ old('short_summary', $profile->short_summary) }}</textarea>
</label>
<label class="block text-sm">Detail profil
    <textarea name="detail_body" rows="8" class="mt-1 w-full rounded-md border border-line px-3 py-2">{{ old('detail_body', $profile->detail_body) }}</textarea>
    <span class="text-xs text-muted">Pisahkan paragraf dengan baris kosong. HTML tidak disimpan.</span>
</label>
<label class="block text-sm">Riwayat pendidikan
    <textarea name="education" rows="5" class="mt-1 w-full rounded-md border border-line px-3 py-2">{{ old('education', $profile->education) }}</textarea>
    <span class="text-xs text-muted">Satu butir tiap baris.</span>
</label>
<label class="block text-sm">Riwayat pekerjaan
    <textarea name="work_history" rows="6" class="mt-1 w-full rounded-md border border-line px-3 py-2">{{ old('work_history', $profile->work_history) }}</textarea>
    <span class="text-xs text-muted">Satu butir tiap baris.</span>
</label>
<label class="block text-sm">Urutan
    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $profile->sort_order ?? 0) }}" class="mt-1 w-full rounded-md border border-line px-3 py-2">
</label>
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $profile->is_published))> Tayang di halaman publik</label>
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $profile->is_featured))> Tampilkan sebagai profil utama</label>
