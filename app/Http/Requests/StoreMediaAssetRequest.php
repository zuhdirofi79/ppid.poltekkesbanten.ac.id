<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ZipArchive;

class StoreMediaAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canDo('manage-pages') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $maxKb = max(1, (int) config('ppid.max_upload_mb', 20)) * 1024;

        return [
            'file' => ['required', 'file', 'max:'.$maxKb],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,internal'],
            'page_id' => ['nullable', 'exists:pages,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $file = $this->file('file');
            if (! $file || $validator->errors()->has('file')) {
                return;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $mime = strtolower((string) $file->getMimeType());
            $allowed = [
                'png' => ['image/png'],
                'jpg' => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'pdf' => ['application/pdf'],
                'doc' => ['application/msword', 'application/vnd.ms-word'],
                'docx' => [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                    'application/octet-stream',
                ],
            ];

            if (! isset($allowed[$extension]) || ! in_array($mime, $allowed[$extension], true)) {
                $validator->errors()->add('file', 'Hanya PNG, JPG, PDF, DOC, dan DOCX yang diizinkan.');

                return;
            }

            if ($extension === 'docx' && $mime !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                $zip = new ZipArchive;
                $opened = $zip->open($file->getRealPath()) === true;
                $hasDocument = $opened && $zip->locateName('word/document.xml') !== false;
                if ($opened) {
                    $zip->close();
                }
                if (! $hasDocument) {
                    $validator->errors()->add('file', 'Berkas DOCX tidak valid.');

                    return;
                }
            }

            $head = strtolower((string) file_get_contents($file->getRealPath(), false, null, 0, 80));
            if (str_contains($head, '<?php') || str_contains($head, '<script') || str_contains($head, '<html') || str_contains($head, '<svg')) {
                $validator->errors()->add('file', 'Isi berkas tidak diizinkan.');
            }
        });
    }
}
