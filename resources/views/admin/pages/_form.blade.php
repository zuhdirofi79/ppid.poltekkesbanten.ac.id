<x-admin.input label="Judul" name="title" :value="$page->title" required />
<x-admin.input label="Slug" name="slug" :value="$page->slug" help="Kosongkan untuk digenerate otomatis." />
<x-admin.textarea label="Ringkasan" name="excerpt" :value="$page->excerpt" :rows="2" />
<x-admin.input label="Meta title" name="meta_title" :value="$page->meta_title" help="Kosongkan untuk memakai judul halaman." />
<x-admin.textarea label="Meta description" name="meta_description" :value="$page->meta_description" :rows="2" help="Kosongkan untuk memakai ringkasan." />
<x-admin.textarea label="Isi" name="body" :value="$page->body" :rows="14" help="HTML aman: p, h2–h4, daftar, tautan, details/summary. Script tidak diizinkan." />
<div class="grid gap-4 sm:grid-cols-3">
    <x-admin.select label="Status" name="status" :value="$page->status" :options="['draft' => 'Draft', 'published' => 'Published']" required />
    <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($page->published_at)->format('Y-m-d\TH:i')" />
    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$page->sort_order ?? 0" />
</div>
