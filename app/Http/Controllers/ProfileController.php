<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    public function maklumatPelayanan(): View
    {
        $imageWebp = 'images/ppid/maklumat-pelayanan-poltekkes-banten.webp';
        $imagePng = 'images/ppid/maklumat-pelayanan-poltekkes-banten.png';
        $absoluteWebp = public_path($imageWebp);
        $exists = is_file($absoluteWebp);

        return view('pages.profile.maklumat-pelayanan', [
            'imageWebp' => $imageWebp,
            'imagePng' => $imagePng,
            'imageExists' => $exists,
            'fileSize' => $exists ? filesize($absoluteWebp) : null,
            'imageWidth' => 1600,
            'imageHeight' => 800,
            'page' => Page::query()->published()->where('slug', 'maklumat-pelayanan')->first(),
        ]);
    }

    public function downloadMaklumatPelayanan(): BinaryFileResponse|Response
    {
        $relative = 'images/ppid/maklumat-pelayanan-poltekkes-banten.webp';
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
            'maklumat-pelayanan-poltekkes-kemenkes-banten.webp',
            ['Content-Type' => 'image/webp'],
        );
    }
}
