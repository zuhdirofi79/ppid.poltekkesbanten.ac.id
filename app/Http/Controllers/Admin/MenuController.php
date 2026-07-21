<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function edit(): View
    {
        $menu = Menu::query()->firstOrCreate(
            ['location' => 'primary'],
            ['name' => 'Navigasi Utama', 'is_active' => true],
        );

        $items = $menu->items()->with('children')->whereNull('parent_id')->orderBy('sort_order')->get();

        return view('admin.menus.edit', compact('menu', 'items'));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $menu = Menu::query()->where('location', 'primary')->firstOrFail();

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'type' => ['required', 'in:route,url,page'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'type' => $data['type'],
            'route_name' => $data['route_name'] ?? null,
            'url' => $data['url'] ?? null,
            'page_slug' => $data['page_slug'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('created', $item, 'Menambah item menu: '.$item->label);

        return redirect()->route('admin.menus.edit')->with('status', 'Item menu ditambahkan.');
    }

    public function updateItem(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'type' => ['required', 'in:route,url,page'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (($data['parent_id'] ?? null) == $menuItem->id) {
            return back()->withErrors(['parent_id' => 'Item tidak boleh menjadi parent dirinya sendiri.']);
        }

        $menuItem->update([
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'type' => $data['type'],
            'route_name' => $data['route_name'] ?? null,
            'url' => $data['url'] ?? null,
            'page_slug' => $data['page_slug'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record('updated', $menuItem, 'Memperbarui item menu: '.$menuItem->label);

        return redirect()->route('admin.menus.edit')->with('status', 'Item menu diperbarui.');
    }

    public function destroyItem(MenuItem $menuItem): RedirectResponse
    {
        ActivityLog::record('deleted', $menuItem, 'Menghapus item menu: '.$menuItem->label);
        $menuItem->delete();

        return redirect()->route('admin.menus.edit')->with('status', 'Item menu dihapus.');
    }
}
