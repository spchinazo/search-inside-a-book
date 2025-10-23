<?php

namespace Tests\Feature;

use App\Book;
use App\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test book and pages
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'A test book for search functionality'
        ]);

        BookPage::create([
            'book_id' => $book->id,
            'page_number' => 1,
            'text_content' => 'This is a test page about JavaScript and DOM manipulation.'
        ]);

        BookPage::create([
            'book_id' => $book->id,
            'page_number' => 2,
            'text_content' => 'Another page with different content about programming.'
        ]);

        BookPage::create([
            'book_id' => $book->id,
            'page_number' => 3,
            'text_content' => 'DOM elements can be manipulated using JavaScript.'
        ]);
    }

    public function test_get_book_info(): void
    {
        $response = $this->getJson('/api/book');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Test Book',
                    'author' => 'Test Author',
                    'total_pages' => 3
                ]
            ]);
    }

    public function test_search_returns_results(): void
    {
        $response = $this->getJson('/api/search?q=JavaScript');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'data' => [
                    'results' => [
                        '*' => [
                            'id',
                            'page_number',
                            'snippet',
                            'relevance_score',
                            'match_position'
                        ]
                    ],
                    'total',
                    'query'
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    public function test_search_with_empty_query(): void
    {
        $response = $this->getJson('/api/search?q=');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_search_pagination(): void
    {
        $response = $this->getJson('/api/search?q=DOM&limit=1&page=1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 1
                ]
            ]);
    }

    public function test_get_specific_page(): void
    {
        $page = BookPage::first();
        
        $response = $this->getJson("/api/page/{$page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $page->id,
                    'page_number' => $page->page_number,
                    'text_content' => $page->text_content
                ]
            ]);
    }

    public function test_get_nonexistent_page(): void
    {
        $response = $this->getJson('/api/page/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Page not found'
            ]);
    }

    public function test_search_highlights_terms(): void
    {
        $response = $this->getJson('/api/search?q=JavaScript');

        $response->assertStatus(200);
        
        $data = $response->json('data.results');
        $this->assertNotEmpty($data);
        
        // Check if snippets contain highlighted terms
        foreach ($data as $result) {
            $this->assertStringContainsString('<mark>', $result['snippet']);
        }
    }

    public function test_search_ranking(): void
    {
        $response = $this->getJson('/api/search?q=DOM');

        $response->assertStatus(200);
        
        $data = $response->json('data.results');
        $this->assertNotEmpty($data);
        
        // Check that results are ordered by relevance
        $scores = array_column($data, 'relevance_score');
        $sortedScores = $scores;
        rsort($sortedScores);
        
        $this->assertEquals($sortedScores, $scores);
    }
}
