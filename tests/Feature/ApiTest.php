<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use MeiliSearch\Client;
use Illuminate\Support\Facades\Artisan;

class ApiTest extends TestCase
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
            'text_content' => 'This is a test page about JavaScript and DOM manipulation. The DOM is used widely.'
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
            $this->meilisearchClient->index($indexName)->delete();
        } catch (\Exception $e) {
        }
        
        parent::tearDown();
    }
    
    public function test_get_book_info(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search?q=test");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_search_returns_results(): void
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

    public function test_search_with_empty_query(): void
    {
        $response = $this->getJson("/api/books/{$this->book->id}/search?q=");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'The "q" parameter is required for search.'
            ]);
    }

    public function test_get_specific_page(): void
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
                'book_title',
            ]);
    }

    public function test_get_nonexistent_page(): void
    {
        $response = $this->getJson('/api/pages/999');

        $response->assertStatus(404); 
    }
}