<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\PublicNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function edit(Request $request): View
    {
        $location = $this->location($request);
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location],
            ['name' => $location === 'footer' ? 'Menu Footer' : 'Navigasi Utama', 'is_active' => true],
        );

        $items = $menu->items()->with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        $source = (string) SiteSetting::getValue('navigation.public_source', 'config');

        return view('admin.menus.edit', compact('menu', 'items', 'location', 'source'));
    }

    public function updateSource(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'public_source' => ['required', 'in:config,database'],
            'confirm_database_nav' => ['required_if:public_source,database', 'accepted'],
        ]);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            [
                'group' => 'navigation',
                'value' => $data['public_source'],
                'type' => 'text',
                'label' => 'Sumber navigasi publik',
            ],
        );

        PublicNavigation::forget();
        ActivityLog::record('menu_updated', null, 'Mengubah sumber navigasi publik menjadi '.$data['public_source']);

        return redirect()->route('admin.menus.edit')->with('status', 'Sumber navigasi publik diperbarui.');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $location = $this->location($request);
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location],
            ['name' => $location === 'footer' ? 'Menu Footer' : 'Navigasi Utama', 'is_active' => true],
        );

        $data = $this->validatedItem($request);
        $this->assertMenuRules($data);

        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'type' => $data['type'],
            'route_name' => $data['route_name'] ?? null,
            'url' => $data['url'] ?? null,
            'page_slug' => $data['page_slug'] ?? null,
            'target' => $request->boolean('open_in_new_tab') ? '_blank' : '_self',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        PublicNavigation::forget();
        ActivityLog::record('menu_created', $item, 'Menambah item menu: '.$item->label);

        return redirect()->route('admin.menus.edit', ['location' => $location])->with('status', 'Item menu ditambahkan.');
    }

    public function updateItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $this->validatedItem($request);

        if (($data['parent_id'] ?? null) == $menuItem->id) {
            return back()->withErrors(['parent_id' => 'Item tidak boleh menjadi parent dirinya sendiri.']);
        }

        $this->assertMenuRules($data, $menuItem);

        $menuItem->update([
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'type' => $data['type'],
            'route_name' => $data['route_name'] ?? null,
            'url' => $data['url'] ?? null,
            'page_slug' => $data['page_slug'] ?? null,
            'target' => $request->boolean('open_in_new_tab') ? '_blank' : '_self',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        PublicNavigation::forget();
        ActivityLog::record('menu_updated', $menuItem, 'Memperbarui item menu: '.$menuItem->label);

        return redirect()->route('admin.menus.edit', ['location' => $menuItem->menu?->location])->with('status', 'Item menu diperbarui.');
    }

    public function moveItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $siblings = MenuItem::query()
            ->where('menu_id', $menuItem->menu_id)
            ->where('parent_id', $menuItem->parent_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        foreach ($siblings as $index => $sibling) {
            $sibling->sort_order = ($index + 1) * 10;
            $sibling->save();
        }

        $index = $siblings->search(fn (MenuItem $sibling) => $sibling->id === $menuItem->id);
        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && isset($siblings[$swapIndex])) {
            $current = $siblings[$index]->sort_order;
            $siblings[$index]->update(['sort_order' => $siblings[$swapIndex]->sort_order]);
            $siblings[$swapIndex]->update(['sort_order' => $current]);
        }

        PublicNavigation::forget();
        ActivityLog::record('menu_reordered', $menuItem, 'Mengubah urutan menu: '.$menuItem->label);

        return back()->with('status', 'Urutan menu disimpan.');
    }

    public function destroyItem(MenuItem $menuItem): RedirectResponse
    {
        $location = $menuItem->menu?->location;
        ActivityLog::record('deleted', $menuItem, 'Menghapus item menu: '.$menuItem->label);
        $menuItem->delete();
        PublicNavigation::forget();

        return redirect()->route('admin.menus.edit', ['location' => $location])->with('status', 'Item menu dihapus.');
    }

    private function location(Request $request): string
    {
        $location = $request->string('location')->toString();

        return in_array($location, ['primary', 'footer'], true) ? $location : 'primary';
    }

    /** @return array<string, mixed> */
    private function validatedItem(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'type' => ['required', 'in:route,url,page'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'location' => ['nullable', 'in:primary,footer'],
        ]);
    }

    /** @param  array<string, mixed>  $data */
    private function assertMenuRules(array $data, ?MenuItem $current = null): void
    {
        if (($data['type'] ?? '') === 'url') {
            $url = trim((string) ($data['url'] ?? ''));
            if (preg_match('#^\s*(javascript|data|vbscript):#i', $url) || ! preg_match('#^https?://#i', $url)) {
                throw ValidationException::withMessages([
                    'url' => 'URL eksternal harus diawali http:// atau https://.',
                ]);
            }
        }

        if (($data['type'] ?? '') === 'page') {
            $page = Page::query()->where('slug', (string) ($data['page_slug'] ?? ''))->first();
            if (! $page || $page->status !== 'published') {
                throw ValidationException::withMessages([
                    'page_slug' => 'Hanya halaman yang sudah terbit yang dapat dipasang di menu publik.',
                ]);
            }
        }

        $parentId = $data['parent_id'] ?? null;
        if ($parentId) {
            $parent = MenuItem::query()->find($parentId);
            if ($parent && ($parent->parent_id || ($current && $parent->id === $current->id))) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Submenu paling dalam dua tingkat.',
                ]);
            }
        }
    }
}
