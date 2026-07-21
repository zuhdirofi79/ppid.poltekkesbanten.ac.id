<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Unit;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::query()->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.units.index', compact('units'));
    }

    public function create(): View
    {
        return view('admin.units.create', ['unit' => new Unit(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['name'], Unit::class);

        $unit = Unit::query()->create($data);
        ActivityLog::record('created', $unit, 'Membuat unit: '.$unit->name);

        return redirect()->route('admin.units.index')->with('status', 'Unit berhasil dibuat.');
    }

    public function edit(Unit $unit): View
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['name'], Unit::class, $unit->id);
        $unit->update($data);
        ActivityLog::record('updated', $unit, 'Memperbarui unit: '.$unit->name);

        return redirect()->route('admin.units.index')->with('status', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        ActivityLog::record('deleted', $unit, 'Menghapus unit: '.$unit->name);
        $unit->delete();

        return redirect()->route('admin.units.index')->with('status', 'Unit berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
