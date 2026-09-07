<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private readonly GlobalSearchService $search) {}

    public function index(Request $request): View
    {
        $raw = (string) $request->query('q', '');
        $normalized = $this->search->normalize($raw);
        $result = $normalized === null
            ? ['query' => trim($raw), 'total' => 0, 'groups' => []]
            : $this->search->search($normalized, 12);

        return view('pages.search', [
            'q' => $result['query'] ?? trim($raw),
            'hasQuery' => $normalized !== null,
            'total' => $result['total'],
            'groups' => $result['groups'],
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $normalized = $this->search->normalize((string) $request->query('q', ''));

        if ($normalized === null) {
            return response()->json([
                'query' => '',
                'results' => [],
            ]);
        }

        return response()->json([
            'query' => $normalized,
            'results' => $this->search->suggestions($normalized, 10),
        ]);
    }
}
