<?php
namespace App\Services;

use App\Constants\EloquentPaginatorConstant;
use App\Models\BookPage;

class BooksService
{
    function queryBooks($term) {
        return \App\Models\Book::query()
            ->select([
                'id',
                'title',
                'disk',
                'lang',
                'isbn',
                'front',
            ])
            ->when($term, function ($query, $term) {
                $query->where('title', 'ilike', "%{$term}%")
                    ->orWhere('isbn', 'ilike', "%{$term}%");
            })
            ->paginate(EloquentPaginatorConstant::$PAGINATION);
    }
    
    public function searchBooks($term)
    {
        $results = $this->queryBooks($term);

        return [
            'items' => $results->getCollection()->map(function ($item) {
                return [
                    'id' => encodeId($item->id),
                    'title' => $item->title,
                    'lang' => $item->lang,
                    'isbn' => $item->isbn,
                    'front' => $item->front,
                ];
            }),
            'count_items' => $results->total(),
            'current_page' => $results->currentPage(),
            'number_paginate' => $results->perPage(),
            'last_page' => $results->lastPage(),
        ];
    }

    public function searchInBook($bookId, $term)
    {
        $book = \App\Models\Book::find($bookId);

        if (!$book) {
            abort(404);
        }

        $lang = $book->lang;
        $config = $lang === 'en' ? 'english' : 'spanish';
        return BookPage::where('book_id', $book->id)
            ->search($term, $lang)
            ->select('id', 'book_id', 'page_number', 'path')
            ->selectRaw("ts_headline('{$config}', content, plainto_tsquery('{$config}', ?)) as snippet", [$term])
            ->paginate(EloquentPaginatorConstant::$PAGINATION);
    }

    public function formatSearchResponse($results)
    {
        return [
            'items' => $results->getCollection()->map(function ($item) {
                return [
                    'page' => $item->page_number,
                    'content' => $item->snippet,
                ];
            }),
            'count_items' => $results->total(),
            'current_page' => $results->currentPage(),
            'number_paginate' => $results->perPage(),
            'last_page' => $results->lastPage(),
        ];
    }
}
