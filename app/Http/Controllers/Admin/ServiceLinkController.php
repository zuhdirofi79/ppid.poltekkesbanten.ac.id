<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ServiceLink;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceLinkController extends Controller
{
    public function index(): View
    {
        $services = ServiceLink::query()->orderBy('sort_order')->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create', [
            'service' => new ServiceLink(['is_active' => true, 'is_featured' => false, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], ServiceLink::class);

        $service = ServiceLink::query()->create($data);
        ActivityLog::record('created', $service, 'Membuat layanan: '.$service->title);

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil dibuat.');
    }

    public function edit(ServiceLink $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, ServiceLink $service): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], ServiceLink::class, $service->id);
        $service->update($data);
        ActivityLog::record('updated', $service, 'Memperbarui layanan: '.$service->title);

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil diperbarui.');
    }

    public function destroy(ServiceLink $service): RedirectResponse
    {
        ActivityLog::record('deleted', $service, 'Menghapus layanan: '.$service->title);
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_external'] = filled($data['external_url'] ?? null);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
