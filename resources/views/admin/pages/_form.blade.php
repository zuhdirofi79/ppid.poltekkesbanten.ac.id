<x-admin.input label="Judul" name="title" :value="$page->title" required />
<x-admin.input label="Slug" name="slug" :value="$page->slug" help="Kosongkan untuk digenerate otomatis." />
<x-admin.textarea label="Ringkasan" name="excerpt" :value="$page->excerpt" :rows="2" />
<x-admin.input label="Meta title" name="meta_title" :value="$page->meta_title" help="Kosongkan untuk memakai judul halaman." />
<x-admin.textarea label="Meta description" name="meta_description" :value="$page->meta_description" :rows="2" help="Kosongkan untuk memakai ringkasan." />
<x-admin.textarea label="Isi" name="body" :value="$page->body" :rows="14" help="HTML aman: p, h2–h4, daftar, tautan, tabel. Gambar hanya dari URL /media/." />
<div class="flex flex-wrap gap-2 text-xs">
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;h2&gt;%%&lt;/h2&gt;')">H2</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;h3&gt;%%&lt;/h3&gt;')">H3</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;strong&gt;%%&lt;/strong&gt;')">Tebal</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;em&gt;%%&lt;/em&gt;')">Miring</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;ul&gt;&lt;li&gt;%%&lt;/li&gt;&lt;/ul&gt;')">Daftar</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;blockquote&gt;&lt;p&gt;%%&lt;/p&gt;&lt;/blockquote&gt;')">Kutipan</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsert('&lt;hr&gt;')">Garis</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsertLink()">Tautan</button>
    <button type="button" class="rounded border border-line px-2 py-1" onclick="ppidInsertMedia()">Gambar media</button>
</div>
<script>
function ppidInsert(snippet) {
    const el = document.getElementById('body');
    if (!el) return;
    const start = el.selectionStart ?? el.value.length;
    const end = el.selectionEnd ?? start;
    const selected = el.value.slice(start, end) || 'teks';
    const next = snippet.replace('%%', selected);
    el.value = el.value.slice(0, start) + next + el.value.slice(end);
    el.focus();
}
function ppidInsertLink() {
    const href = window.prompt('URL http(s) atau path /halaman/...');
    if (!href || /^(javascript|data):/i.test(href)) return;
    ppidInsert('<a href="' + href.replace(/"/g, '') + '">%%</a>');
}
function ppidInsertMedia() {
    const url = window.prompt('URL media library, diawali /media/');
    if (!url || !url.startsWith('/media/')) {
        window.alert('Gunakan URL dari Media Library.');
        return;
    }
    ppidInsert('<img src="' + url.replace(/"/g, '') + '" alt="%%">');
}
</script>
<div class="grid gap-4 sm:grid-cols-3">
    <x-admin.select label="Status" name="status" :value="$page->status" :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Arsip']" required />
    <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($page->published_at)->format('Y-m-d\TH:i')" />
    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$page->sort_order ?? 0" />
</div>
