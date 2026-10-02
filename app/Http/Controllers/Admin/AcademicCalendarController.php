<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $calendars = AcademicCalendar::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->yearOrdered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.academic-calendars.index', compact('calendars'));
    }

    public function create(): View
    {
        return view('admin.academic-calendars.create', [
            'calendar' => new AcademicCalendar([
                'is_published' => true,
                'source_type' => AcademicCalendar::SOURCE_UPLOADED,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data = $this->handleUpload($request, $data);
        $data = $this->syncMimeFromPath($data);

        $calendar = AcademicCalendar::query()->create($data);
        $calendar->syncSourceFlags();
        $calendar->save();

        ActivityLog::record('created', $calendar, 'Membuat kalender akademik: '.$calendar->title);

        return redirect()
            ->route('admin.academic-calendars.index')
            ->with('status', 'Kalender akademik berhasil dibuat.');
    }

    public function edit(AcademicCalendar $academicCalendar): View
    {
        return view('admin.academic-calendars.edit', [
            'calendar' => $academicCalendar,
        ]);
    }

    public function update(Request $request, AcademicCalendar $academicCalendar): RedirectResponse
    {
        $data = $this->validated($request, $academicCalendar);
        $data['updated_by'] = $request->user()->id;
        $data = $this->handleUpload($request, $data, $academicCalendar);
        $data = $this->syncMimeFromPath($data, $academicCalendar);

        $academicCalendar->fill($data);
        $academicCalendar->syncSourceFlags();
        $academicCalendar->save();

        ActivityLog::record('updated', $academicCalendar, 'Memperbarui kalender akademik: '.$academicCalendar->title);

        return redirect()
            ->route('admin.academic-calendars.index')
            ->with('status', 'Kalender akademik berhasil diperbarui.');
    }

    public function destroy(AcademicCalendar $academicCalendar): RedirectResponse
    {
        // Delete uploaded file if exists
        if ($academicCalendar->file_path && Storage::disk('public')->exists($academicCalendar->file_path)) {
            Storage::disk('public')->delete($academicCalendar->file_path);
        }

        ActivityLog::record('deleted', $academicCalendar, 'Menghapus kalender akademik: '.$academicCalendar->title);
        $academicCalendar->delete();

        return redirect()
            ->route('admin.academic-calendars.index')
            ->with('status', 'Kalender akademik berhasil dihapus.');
    }

    public function removeFile(AcademicCalendar $academicCalendar): RedirectResponse
    {
        $oldPath = $academicCalendar->file_path;

        $academicCalendar->update([
            'file_path' => null,
            'mime_type' => $academicCalendar->hasPublicAsset() ? $academicCalendar->getMimeType() : null,
            'extension' => $academicCalendar->hasPublicAsset() ? $academicCalendar->getExtension() : null,
            'source_type' => $academicCalendar->hasPublicAsset()
                ? AcademicCalendar::SOURCE_PUBLIC_ASSET
                : ($academicCalendar->hasSafeExternalUrl()
                    ? AcademicCalendar::SOURCE_EXTERNAL
                    : AcademicCalendar::SOURCE_UPLOADED),
        ]);

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        ActivityLog::record('removed_file', $academicCalendar, 'Menghapus file kalender akademik: '.$academicCalendar->title);

        return back()->with('status', 'File berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?AcademicCalendar $calendar = null): array
    {
        $data = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                Rule::unique('academic_calendars', 'year')->ignore($calendar?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'source_type' => ['required', Rule::in([
                AcademicCalendar::SOURCE_UPLOADED,
                AcademicCalendar::SOURCE_EXTERNAL,
                AcademicCalendar::SOURCE_PUBLIC_ASSET,
            ])],
            'external_url' => ['nullable', 'string', 'max:500'],
            'public_asset_path' => ['nullable', 'string', 'max:500'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'published_at' => ['nullable', 'date'],
            'file' => [
                'nullable',
                'file',
                'max:20480', // 20MB
                'mimes:pdf,png,jpg,jpeg',
                'mimetypes:application/pdf,image/png,image/jpeg',
            ],
        ]);

        $source = $data['source_type'];

        if ($source === AcademicCalendar::SOURCE_EXTERNAL) {
            $url = trim((string) ($data['external_url'] ?? ''));
            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                throw ValidationException::withMessages([
                    'external_url' => 'URL eksternal wajib valid.',
                ]);
            }
            $data['external_url'] = $url;
            $data['public_asset_path'] = null;
        } elseif ($source === AcademicCalendar::SOURCE_PUBLIC_ASSET) {
            $path = trim((string) ($data['public_asset_path'] ?? ''));
            if ($path === '') {
                throw ValidationException::withMessages([
                    'public_asset_path' => 'Path asset publik wajib diisi.',
                ]);
            }
            // Validate extension of public asset path
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (! in_array($ext, AcademicCalendar::ALLOWED_EXTENSIONS, true)) {
                throw ValidationException::withMessages([
                    'public_asset_path' => 'Format file harus PDF, PNG, JPG, atau JPEG.',
                ]);
            }
            $data['public_asset_path'] = $path;
            $data['external_url'] = null;
        } else {
            $data['external_url'] = null;
            $data['public_asset_path'] = null;
        }

        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? $calendar?->sort_order ?? $data['year']);

        unset($data['file']);

        return $data;
    }

    /**
     * Handle file upload for PDF, PNG, JPG, JPEG.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function handleUpload(Request $request, array $data, ?AcademicCalendar $calendar = null): array
    {
        if (! $request->hasFile('file')) {
            return $data;
        }

        if (($data['source_type'] ?? '') !== AcademicCalendar::SOURCE_UPLOADED) {
            return $data;
        }

        $file = $request->file('file');
        $mime = (string) $file->getMimeType();
        $allowedMimes = ['application/pdf', 'image/png', 'image/jpeg'];

        if (! in_array($mime, $allowedMimes, true)) {
            throw ValidationException::withMessages([
                'file' => 'Hanya berkas PDF, PNG, JPG, atau JPEG yang diizinkan.',
            ]);
        }

        // Determine extension from MIME
        $extension = match ($mime) {
            'application/pdf' => 'pdf',
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            default => 'pdf',
        };

        $year = $data['year'] ?? $calendar?->year ?? date('Y');
        $safeName = 'kalender-akademik-'.$year.'-'.Str::lower(Str::random(8)).'.'.$extension;
        $path = $file->storeAs('ppid/academic-calendars', $safeName, 'public');

        $oldPath = $calendar?->file_path;

        $data['file_path'] = $path;
        $data['mime_type'] = $mime;
        $data['extension'] = $extension;
        $data['source_type'] = AcademicCalendar::SOURCE_UPLOADED;
        $data['external_url'] = null;
        $data['public_asset_path'] = null;

        // Clean up old file if replaced
        if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $data;
    }

    /**
     * Sync MIME type and extension from public_asset_path if not uploading.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function syncMimeFromPath(array $data, ?AcademicCalendar $calendar = null): array
    {
        // If we already have mime_type from upload, skip
        if (isset($data['mime_type']) && filled($data['mime_type'])) {
            return $data;
        }

        $path = $data['public_asset_path'] ?? $data['file_path'] ?? $calendar?->public_asset_path ?? $calendar?->file_path;

        if (filled($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $data['extension'] = $ext;
            $data['mime_type'] = match ($ext) {
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                default => null,
            };
        }

        return $data;
    }
}
