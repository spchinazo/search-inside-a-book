<?php

namespace App\Http\Controllers;

use App\Services\BookContent;
use App\Services\BookSearch;
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
}
