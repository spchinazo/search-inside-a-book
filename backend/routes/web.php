<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Http\Request;
use App\Services\BookContent;
use App\Services\BookSearch;

Route::get('/pages/{page}', function (int $page, Request $request, BookContent $content, BookSearch $search) {
    if ($page < 1) {
        abort(404);
    }

    $total = $content->totalPages();
    $data = $content->getPage($page);

    if ($data === null || $page > $total) {
        abort(404);
    }

    $term = trim((string) $request->query('q', ''));
    $resultPage = max(1, (int) $request->query('p', 1));
    $perPage = max(1, min(100, (int) $request->query('per_page', 12)));
    $gridResults = [];
    $pagesWithTerm = [];
    $gridMeta = [
        'total' => 0,
        'total_pages' => 1,
        'current_page' => $resultPage,
        'per_page' => $perPage,
    ];

    if (mb_strlen($term) >= 2) {
        // Reuse search to show matches in a grid with pagination.
        $searchResult = $search->search($term, $perPage, 1, ($resultPage - 1) * $perPage);
        $gridResults = $searchResult['data'];
        $gridMeta['total'] = $searchResult['total'];
        $gridMeta['total_pages'] = $perPage > 0 ? (int) ceil($searchResult['total'] / $perPage) : 1;

        // Build a compact list of pages where the term appears (deduped, one per page).
        $allMatches = $search->search($term, 0, 1, 0)['data'];
        $pagesWithTerm = array_values(array_unique(array_map(fn ($item) => $item['page_id'], $allMatches)));
    }

    return view('page', [
        'page' => $data['page'],
        'content' => $data['content'],
        'totalPages' => $total,
        'term' => $term,
        'gridResults' => $gridResults,
        'gridMeta' => $gridMeta,
        'pagesWithTerm' => $pagesWithTerm,
    ]);
})->whereNumber('page');
