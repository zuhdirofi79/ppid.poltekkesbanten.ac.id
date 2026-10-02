<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LeadershipProfile;
use App\Services\MediaLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeadershipProfileController extends Controller
{
    public function index(): View
    {
        $profiles = LeadershipProfile::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.leadership-profiles.index', compact('profiles'));
    }

    public function create(): View
    {
        return view('admin.leadership-profiles.create', [
            'profile' => new LeadershipProfile(['is_published' => true, 'sort_order' => 50]),
        ]);
    }

    public function store(Request $request, MediaLibrary $library): RedirectResponse
    {
        $data = $this->validated($request);
        $data['position_key'] = $this->uniqueKey($data['position'], $data['name']);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['published_at'] = $data['is_published'] ? now() : null;

        $profile = LeadershipProfile::query()->create($data);
        $this->storePhoto($request, $profile, $library);
        ActivityLog::record('leadership_profile_created', $profile, 'Menambah profil pimpinan: '.$profile->displayName());

        return redirect()->route('admin.leadership-profiles.index')->with('status', 'Profil pimpinan ditambahkan.');
    }

    public function edit(LeadershipProfile $leadershipProfile): View
    {
        return view('admin.leadership-profiles.edit', ['profile' => $leadershipProfile]);
    }

    public function update(Request $request, LeadershipProfile $leadershipProfile, MediaLibrary $library): RedirectResponse
    {
        $data = $this->validated($request);
        $data['updated_by'] = $request->user()->id;
        if ($data['is_published'] && ! $leadershipProfile->published_at) {
            $data['published_at'] = now();
        }

        $leadershipProfile->update($data);
        $this->storePhoto($request, $leadershipProfile, $library);
        ActivityLog::record('leadership_profile_updated', $leadershipProfile, 'Memperbarui profil pimpinan: '.$leadershipProfile->displayName());

        return redirect()->route('admin.leadership-profiles.index')->with('status', 'Profil pimpinan disimpan.');
    }

    public function publish(LeadershipProfile $leadershipProfile): RedirectResponse
    {
        $leadershipProfile->is_published = ! $leadershipProfile->is_published;
        if ($leadershipProfile->is_published) {
            $leadershipProfile->published_at ??= now();
        }
        $leadershipProfile->save();

        return back()->with('status', $leadershipProfile->is_published ? 'Profil ditayangkan.' : 'Profil disembunyikan dari halaman publik.');
    }

    public function destroy(LeadershipProfile $leadershipProfile): RedirectResponse
    {
        ActivityLog::record('leadership_profile_deleted', $leadershipProfile, 'Menghapus profil pimpinan: '.$leadershipProfile->displayName());
        $leadershipProfile->delete();

        return redirect()->route('admin.leadership-profiles.index')->with('status', 'Profil pimpinan dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'credentials' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'short_summary' => ['nullable', 'string', 'max:5000'],
            'detail_body' => ['nullable', 'string', 'max:20000'],
            'education' => ['nullable', 'string', 'max:10000'],
            'work_history' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'file', 'max:5120'],
        ]);

        foreach (['name', 'credentials', 'position', 'short_summary', 'detail_body', 'education', 'work_history'] as $field) {
            $value = trim(strip_tags((string) ($data[$field] ?? '')));
            $data[$field] = $value === '' ? null : $value;
        }

        if ($data['name'] === null || $data['position'] === null) {
            throw ValidationException::withMessages([
                'name' => 'Nama dan jabatan wajib diisi.',
            ]);
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $this->assertPhoto($request);
        }

        return $data;
    }

    private function assertPhoto(Request $request): void
    {
        $file = $request->file('photo');
        $extension = strtolower((string) $file?->getClientOriginalExtension());
        $mime = strtolower((string) $file?->getMimeType());
        $allowed = [
            'png' => ['image/png'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'webp' => ['image/webp'],
        ];

        if (! isset($allowed[$extension]) || ! in_array($mime, $allowed[$extension], true)) {
            throw ValidationException::withMessages([
                'photo' => 'Foto harus berupa PNG, JPG, JPEG, atau WebP.',
            ]);
        }
    }

    private function storePhoto(Request $request, LeadershipProfile $profile, MediaLibrary $library): void
    {
        if (! $request->hasFile('photo')) {
            return;
        }

        $asset = $library->store($request->file('photo'), [
            'title' => $profile->displayName(),
            'alt_text' => $profile->displayName().' — '.$profile->position,
            'visibility' => 'public',
        ], $request->user()?->id);

        $profile->update(['photo_media_asset_id' => $asset->id]);
    }

    private function uniqueKey(string $position, string $name): string
    {
        $base = Str::slug($position) ?: Str::slug($name) ?: 'pimpinan';
        $key = $base;
        $i = 2;

        while (LeadershipProfile::query()->where('position_key', $key)->exists()) {
            $key = $base.'-'.$i;
            $i++;
        }

        return $key;
    }
}
