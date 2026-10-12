<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    private const IMAGE_DIRECTORY = 'docs';

    private const IMAGE_FILENAME = 'MAKLUMAT PELAYANAN.png';

    private const DOWNLOAD_NAME = 'maklumat-pelayanan-poltekkes-kemenkes-banten.png';

    public function maklumatPelayanan(): View
    {
        $relative = $this->imageRelativePath();
        $absolute = public_path($relative);
        $exists = is_file($absolute);
        $imageWidth = null;
        $imageHeight = null;

        if ($exists) {
            $size = @getimagesize($absolute);
            if (is_array($size)) {
                $imageWidth = $size[0] ?: null;
                $imageHeight = $size[1] ?: null;
            }
        }

        return view('pages.profile.maklumat-pelayanan', [
            'imageUrl' => $this->imagePublicUrl(),
            'fileSize' => $exists ? filesize($absolute) : null,
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight,
            'page' => Page::query()->published()->where('slug', 'maklumat-pelayanan')->first(),
        ]);
    }

    public function downloadMaklumatPelayanan(): BinaryFileResponse|Response
    {
        $relative = $this->imageRelativePath();
        $path = public_path($relative);

        if (! is_file($path) || str_contains($relative, '..')) {
            abort(404, 'Berkas Maklumat Pelayanan tidak ditemukan.');
        }

        $real = realpath($path);
        $publicRoot = realpath(public_path());

        if ($real === false || $publicRoot === false || ! str_starts_with($real, $publicRoot)) {
            abort(403);
        }

        return response()->download(
            $real,
            self::DOWNLOAD_NAME,
            ['Content-Type' => 'image/png'],
        );
    }

    private function imageRelativePath(): string
    {
        return self::IMAGE_DIRECTORY.'/'.self::IMAGE_FILENAME;
    }

    private function imagePublicUrl(): string
    {
        return asset(self::IMAGE_DIRECTORY.'/'.rawurlencode(self::IMAGE_FILENAME));
    }
}
