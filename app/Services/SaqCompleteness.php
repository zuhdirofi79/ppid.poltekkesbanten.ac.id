<?php

namespace App\Services;

use App\Models\SaqEvidence;
use App\Models\SaqItem;
use Illuminate\Support\Facades\Storage;

class SaqCompleteness
{
    /**
     * @return array{status: string, public_url: ?string, evidence_count: int, public_state: string, has_public: bool}
     */
    public function assess(SaqItem $item): array
    {
        $item->loadMissing(['evidences.page', 'evidences.document', 'evidences.media']);

        $publicUrl = null;
        $hasPublic = false;
        $hasDraft = false;
        $hasInternal = false;

        foreach ($item->evidences as $evidence) {
            $state = $this->classify($evidence);

            if ($state === 'public') {
                $hasPublic = true;
                $publicUrl ??= $this->urlFor($evidence);
            } elseif ($state === 'draft') {
                $hasDraft = true;
            } elseif ($state === 'internal') {
                $hasInternal = true;
            }
        }

        if ($item->evidences->isEmpty()) {
            $base = 'missing';
        } elseif ($hasPublic) {
            $base = 'complete';
        } elseif ($hasDraft) {
            $base = 'draft';
        } else {
            $base = 'needs_review';
        }

        $status = $base;
        if ($base === 'complete' && $item->status === 'published') {
            $status = 'published';
        } elseif ($base === 'complete' && $item->status === 'needs_review') {
            $status = 'needs_review';
        }

        $publicState = 'missing';
        if ($hasPublic) {
            $publicState = 'available';
        } elseif ($hasDraft) {
            $publicState = 'draft';
        } elseif ($hasInternal) {
            $publicState = 'internal';
        }

        return [
            'status' => $status,
            'public_url' => $publicUrl,
            'evidence_count' => $item->evidences->count(),
            'public_state' => $publicState,
            'has_public' => $hasPublic,
        ];
    }

    public function persist(SaqItem $item, ?string $requested = null): array
    {
        $before = $item->status;
        $assessment = $this->assess($item);

        if ($requested === 'published' && $assessment['has_public']) {
            $item->status = 'published';
            $item->published_at ??= now();
        } elseif ($requested === 'needs_review') {
            $item->status = 'needs_review';
        } elseif ($requested === null) {
            $item->status = $assessment['status'];
        } else {
            $item->status = $assessment['has_public'] && $requested === 'complete'
                ? 'complete'
                : $assessment['status'];
        }

        if (in_array($item->status, ['complete', 'published'], true)) {
            $item->completed_at ??= now();
        }

        $item->save();

        return $this->assess($item->fresh(['evidences.page', 'evidences.document', 'evidences.media']) ?? $item) + [
            'changed' => $before !== $item->status,
        ];
    }

    private function classify(SaqEvidence $evidence): string
    {
        if ($evidence->visibility === 'internal') {
            return 'internal';
        }

        return match ($evidence->evidence_type) {
            'page' => $this->classifyPage($evidence),
            'public_document' => $evidence->document && $evidence->document->isAvailable() ? 'public' : 'missing',
            'media', 'screenshot' => $this->classifyMedia($evidence),
            'external_url' => $this->isPublicUrl($evidence->external_url) ? 'public' : 'missing',
            default => 'missing',
        };
    }

    private function classifyPage(SaqEvidence $evidence): string
    {
        $page = $evidence->page;
        if (! $page) {
            return 'missing';
        }

        if ($page->status === 'published') {
            return 'public';
        }

        if ($page->status === 'draft') {
            return 'draft';
        }

        return 'missing';
    }

    private function classifyMedia(SaqEvidence $evidence): string
    {
        $media = $evidence->media;
        if (! $media) {
            return 'missing';
        }

        if ($media->visibility !== 'public' || $media->disk !== 'public') {
            return 'internal';
        }

        if (! Storage::disk('public')->exists($media->path)) {
            return 'missing';
        }

        return 'public';
    }

    private function urlFor(SaqEvidence $evidence): ?string
    {
        if ($evidence->visibility === 'internal') {
            return null;
        }

        if ($evidence->evidence_type === 'page' && $evidence->page && $evidence->page->status === 'published') {
            return route('pages.show', $evidence->page->slug);
        }

        if ($evidence->evidence_type === 'public_document' && $evidence->document && $evidence->document->isAvailable()) {
            return route('documents.show', $evidence->document->slug);
        }

        if (in_array($evidence->evidence_type, ['media', 'screenshot'], true)) {
            return $evidence->media?->publicUrl();
        }

        if ($evidence->evidence_type === 'external_url' && $this->isPublicUrl($evidence->external_url)) {
            $url = trim((string) $evidence->external_url);

            return str_starts_with($url, '/') ? url($url) : $url;
        }

        return null;
    }

    private function isPublicUrl(?string $url): bool
    {
        $url = trim((string) $url);
        if ($url === '' || preg_match('#^\s*(javascript|data|vbscript):#i', $url)) {
            return false;
        }

        return (bool) preg_match('#^https?://#i', $url) || (bool) preg_match('#^/[\w\-./]+$#', $url);
    }
}
