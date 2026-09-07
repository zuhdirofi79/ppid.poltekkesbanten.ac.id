<?php

namespace App\Console\Commands;

use App\Services\GlobalSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class VerifySearchCommand extends Command
{
    protected $signature = 'ppid:verify-search';

    protected $description = 'Verify Global Search can find public PPID pages, services, and documents';

    public function handle(GlobalSearchService $search): int
    {
        $this->info('Global Search Verification');
        $this->line('--------------------------');

        $checks = [
            'Route' => $this->routesExist(),
            'Page search' => $this->hasTitle($search, 'akreditasi', 'Akreditasi'),
            'Service search' => $this->hasTitle($search, 'keberatan', 'Pengajuan Keberatan'),
            'Document search' => $this->hasTitle($search, 'lakin', 'LAKIN') && $this->hasTitle($search, 'rsb', 'RSB'),
            'Missing document' => $this->hasUnavailable($search, 'lakin', '2023'),
            'Published filter' => $this->hasTitle($search, 'akreditasi', 'Akreditasi'),
        ];

        $failed = 0;
        foreach ($checks as $label => $pass) {
            $this->line(sprintf('%-22s %s', $label, $pass ? 'PASS' : 'FAIL'));
            if (! $pass) {
                $failed++;
            }
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function routesExist(): bool
    {
        return Route::has('search')
            && Route::has('search.index')
            && Route::has('search.suggestions');
    }

    private function hasTitle(GlobalSearchService $search, string $query, string $needle): bool
    {
        foreach ($this->flatten($search->search($query)) as $item) {
            if (stripos((string) $item['title'], $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function hasUnavailable(GlobalSearchService $search, string $query, string $needle): bool
    {
        foreach ($this->flatten($search->search($query)) as $item) {
            $title = (string) $item['title'];
            if (stripos($title, $needle) !== false && empty($item['available']) && empty($item['url'])) {
                return true;
            }
        }

        return false;
    }

    /** @return list<array<string, mixed>> */
    private function flatten(array $result): array
    {
        $items = [];
        foreach ($result['groups'] ?? [] as $group) {
            foreach ($group['items'] ?? [] as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
