<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use MeiliSearch\Client;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected Book $book;
    protected BookPage $bookPage;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'A test book for search functionality'
        ]);

        BookPage::create([
            'book_id' => $this->book->id,
            'page_number' => 1,
            'text_content' => 'This is a test page about JavaScript and DOM manipulation. The DOM is great.'
        ]);

        $this->bookPage = BookPage::create([
            'book_id' => $this->book->id,
            'page_number' => 2,
            'text_content' => 'Another page with different content about programming.'
        ]);

        BookPage::create([
            'book_id' => $this->book->id,
            'page_number' => 3,
            'text_content' => 'DOM elements can be manipulated using JavaScript.'
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        $this->artisan('scout:sync-index-settings'); 
    }
    
    protected function tearDown(): void
    {
        $indexName = (new BookPage())->searchableAs();
        
        try {
            (new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key')))
                ->index($indexName)
                ->delete();
        } catch (\Exception $e) {
        }
        
        parent::tearDown();
    }
    
    /** @test */
    public function test_search_returns_results_and_correct_structure(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search?q=JavaScript");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'page_number',
                        'snippet',
                        'full_page_url' 
                    ]
                ]
            ]);
    }
    
    /** @test */
    public function test_search_requires_query_parameter(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'O parâmetro "q" é obrigatório para a busca.'
            ]);
    }

    /** @test */
    public function test_search_highlights_terms(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search?q=DOM");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        
        $hasHighlighted = collect($data)->contains(function ($result) {
            return str_contains($result['snippet'], '<em>DOM</em>');
        });
        
        $this->assertTrue($hasHighlighted, 'At least one result should have highlighted terms.');
    }

    /** @test */
    public function test_get_specific_page_returns_full_content(): void
    {
        $pageId = $this->bookPage->id; 
        
        $response = $this->getJson("/api/pages/{$pageId}");

        $response->assertStatus(200)
            ->assertJson([
                'page_number' => 2,
                'content' => 'Another page with different content about programming.',
                'book_title' => 'Test Book',
            ])
            ->assertJsonStructure([
                'page_number',
                'content',
                'book_title'
            ]);
    }

    /** @test */
    public function test_get_nonexistent_page_returns_404(): void
    {
        $response = $this->getJson('/api/pages/99999');

        $response->assertStatus(404);
    }
    
}