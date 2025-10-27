<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use MeiliSearch\Client;
use Illuminate\Support\Facades\Artisan;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected Book $book;
    protected BookPage $bookPage;
    protected Client $meilisearchClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->meilisearchClient = new Client(
            config('scout.meilisearch.host'), 
            config('scout.meilisearch.key')
        );

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
        
        Artisan::call('scout:import', ['model' => 'App\Models\BookPage']);
        
        $task = $this->meilisearchClient->index((new BookPage())->searchableAs())
            ->updateFilterableAttributes(['book_id']);
        
        $this->meilisearchClient->index((new BookPage())->searchableAs())
             ->waitForTask($task['taskUid'], 5000); 

    }
    
    protected function tearDown(): void
    {
        $indexName = (new BookPage())->searchableAs();
        
        try {
            $meiliClient = $this->meilisearchClient ?? new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
            $meiliClient->index($indexName)->delete();
        } catch (\Exception $e) {
        }
        
        parent::tearDown();
    }
    
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
    
    public function test_search_requires_query_parameter(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'The "q" parameter is required for search.' 
            ]);
    }

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

    public function test_get_nonexistent_page_returns_404(): void
    {
        $response = $this->getJson('/api/pages/99999');

        $response->assertStatus(404);
    }
    
}