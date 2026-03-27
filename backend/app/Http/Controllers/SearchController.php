<?php

namespace App\Http\Controllers;

use App\Services\BookContent;
use App\Services\BookSearch;
use App\Models\SearchQuery;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly BookSearch $search,
        private readonly BookContent $content,
    ) {
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'max_per_page' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $perPage = $validated['per_page'] ?? 20;
        $page = $validated['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $searchResult = $this->search->search(
            $validated['q'],
            $perPage,
            $validated['max_per_page'] ?? 1,
            $offset,
        );

        $data = $searchResult['data'];
        $total = $searchResult['total'];
        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        $this->persistQuery($validated['q'], $total);

        return response()->json([
            'data' => $data,
            'meta' => [
                'term' => $validated['q'],
                'count' => count($data),
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function page(int $page): JsonResponse
    {
        if ($page < 1) {
            abort(404);
        }

        $total = $this->content->totalPages();
        $data = $this->content->getPage($page);

        if ($data === null || $page > $total) {
            abort(404);
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'total_pages' => $total,
            ],
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['sometimes', 'string'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ]);

        $prefix = Str::lower(trim($validated['q'] ?? ''));
        $limit = $validated['limit'] ?? 8;

        $query = SearchQuery::query()->orderByDesc('times')->orderByDesc('last_used_at');

        if ($prefix !== '') {
            $query->where('term', 'like', $prefix . '%');
        }

        $suggestions = $query
            ->limit($limit)
            ->get(['term', 'times', 'hits_count', 'last_used_at']);

        return response()->json(['data' => $suggestions]);
    }

    private function persistQuery(string $rawTerm, int $hits): void
    {
        $term = Str::lower(trim($rawTerm));
        if ($term === '') {
            return;
        }

        $now = now();

        DB::transaction(function () use ($term, $hits, $now): void {
            $existing = SearchQuery::query()->where('term', $term)->lockForUpdate()->first();

            if (! $existing) {
                SearchQuery::create([
                    'term' => $term,
                    'times' => 1,
                    'hits_count' => max(0, $hits),
                    'last_used_at' => $now,
                ]);
                return;
            }

            $existing->increment('times');
            if ($hits > 0) {
                $existing->increment('hits_count', $hits);
            }
            $existing->forceFill(['last_used_at' => $now])->save();
        });
    }
}
