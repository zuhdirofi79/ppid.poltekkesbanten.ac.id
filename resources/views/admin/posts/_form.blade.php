<x-admin.input label="Judul" name="title" :value="$post->title" required />
<x-admin.input label="Slug" name="slug" :value="$post->slug" />
<x-admin.select
    label="Kategori"
    name="post_category_id"
    :value="$post->post_category_id"
    placeholder="— Pilih —"
    :options="$categories->pluck('name', 'id')->all()"
/>
<x-admin.textarea label="Ringkasan" name="excerpt" :value="$post->excerpt" :rows="2" />
<x-admin.textarea label="Isi" name="body" :value="$post->body" :rows="10" />
<div class="grid gap-4 sm:grid-cols-2">
    <x-admin.select label="Status" name="status" :value="$post->status" :options="['draft' => 'Draft', 'published' => 'Published']" required />
    <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($post->published_at)->format('Y-m-d\TH:i')" />
</div>
<x-admin.checkbox name="is_featured" label="Tandai sebagai unggulan" :checked="$post->is_featured" />
