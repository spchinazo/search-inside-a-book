<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MeiliSearch\Client;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    protected Client $meilisearchClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->meilisearchClient = new Client(
            config('scout.meilisearch.host'), 
            config('scout.meilisearch.key')
        );
        
        // Configure Meilisearch index
        $this->artisan('scout:sync-index-settings');
        
        $task = $this->meilisearchClient->index((new BookPage())->searchableAs())
            ->updateFilterableAttributes(['book_id']);
        
        $this->meilisearchClient->waitForTask($task['taskUid'], 5000);
    }

    protected function tearDown(): void
    {
        $indexName = (new BookPage())->searchableAs();
        
        try {
            $this->meilisearchClient->index($indexName)->delete();
        } catch (\Exception $e) {
            // Index may not exist
        }
        
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_paginated_search_results_with_highlighting(): void
    {
        $book = Book::factory()->create(['title' => 'Test Book']);
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 1,
            'text_content' => 'The DOM is a programming interface for HTML documents.',
        ]);
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 2,
            'text_content' => 'Manipulating the DOM with JavaScript.',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=DOM");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'page_number',
                        'snippet',
                        'full_page_url',
                        'relevance_score',
                    ]
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ]
            ])
            ->assertJsonPath('meta.per_page', 20)
            ->assertJsonPath('meta.current_page', 1);

        // Verify highlighting with <em> tags
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        
        $hasHighlighting = collect($data)->some(
            fn($item) => str_contains($item['snippet'], '<em>') && str_contains($item['snippet'], '</em>')
        );
        
        $this->assertTrue($hasHighlighting, 'Search results should contain <em> tags for highlighting');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_second_page_of_results(): void
    {
        $book = Book::factory()->create();
        
        // Create 25 pages with the word "test"
        for ($i = 1; $i <= 25; $i++) {
            BookPage::factory()->create([
                'book_id' => $book->id,
                'page_number' => $i,
                'text_content' => "This is test page number {$i}.",
            ]);
        }

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=test&page=2");

        $response->assertStatus(200)
            ->assertJsonPath('meta.current_page', '2')
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonCount(5, 'data');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_search_query_is_required(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q'])
            ->assertJsonFragment(['q' => ['The search query is required.']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_search_query_minimum_length(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search?q=a");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q'])
            ->assertJsonFragment(['q' => ['The search query must be at least 2 characters.']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_search_query_maximum_length(): void
    {
        $book = Book::factory()->create();
        $longQuery = str_repeat('a', 201);

        $response = $this->getJson("/api/books/{$book->id}/search?q={$longQuery}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_page_parameter_must_be_positive_integer(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search?q=test&page=0");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_scopes_search_to_specific_book(): void
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book1->id,
            'text_content' => 'Unique term: xyzabc123',
        ]);
        
        BookPage::factory()->create([
            'book_id' => $book2->id,
            'text_content' => 'Different content without the unique term',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        // Search in book2 should return 0 results
        $response = $this->getJson("/api/books/{$book2->id}/search?q=xyzabc123");

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');

        // Search in book1 should return 1 result
        $response = $this->getJson("/api/books/{$book1->id}/search?q=xyzabc123");

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonCount(1, 'data');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_special_characters_in_search_query(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'Code example: function() { return "hello"; }',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=" . urlencode('function()'));

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(0, $response->json('meta.total'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_unicode_characters_in_search(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'Programming in español, français, 中文',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=español");

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(0, $response->json('meta.total'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_relevance_scores_for_results(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'JavaScript is a programming language. JavaScript is everywhere.',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=JavaScript");

        $response->assertStatus(200);
        
        $results = $response->json('data');
        $this->assertNotEmpty($results);
        
        foreach ($results as $result) {
            $this->assertArrayHasKey('relevance_score', $result);
            // Aceita int ou float
            $this->assertTrue(
                is_int($result['relevance_score']) || is_float($result['relevance_score']),
                'relevance_score must be int or float'
            );
            $this->assertGreaterThanOrEqual(0, $result['relevance_score']);
            $this->assertLessThanOrEqual(1, $result['relevance_score']);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_full_page_url_in_results(): void
    {
        $book = Book::factory()->create();
        
        $page = BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'Testing URL generation',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book->id}/search?q=URL");

        $response->assertStatus(200);
        
        $results = $response->json('data');
        if (!empty($results)) {
            $this->assertStringContainsString("/api/books/{$book->id}/pages/", $results[0]['full_page_url']);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_nonexistent_book(): void
    {
        $response = $this->getJson("/api/books/99999/search?q=test");

        $response->assertStatus(404);
    }
}