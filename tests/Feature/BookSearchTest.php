<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure Meilisearch settings are configured
        $this->artisan('scout:sync-index-settings');
        sleep(1); // Wait for async config
    }

    /** @test */
    public function it_returns_paginated_search_results_with_highlighting()
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

        // Import to Meilisearch
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2); // Wait for indexing

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
            ->assertJsonFragment(['page_number' => 1])
            ->assertJsonFragment(['page_number' => 2]);

        // Verify highlighting
        $snippets = collect($response->json('data'))->pluck('snippet');
        $this->assertTrue($snippets->contains(fn($s) => str_contains($s, '<em>')));
    }

    /** @test */
    public function it_validates_search_query_is_required()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }

    /** @test */
    public function it_validates_search_query_minimum_length()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}/search?q=a");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }

    /** @test */
    public function it_scopes_search_to_specific_book()
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book1->id,
            'text_content' => 'Unique term: xyzabc',
        ]);
        
        BookPage::factory()->create([
            'book_id' => $book2->id,
            'text_content' => 'Different content',
        ]);

        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);

        $response = $this->getJson("/api/books/{$book2->id}/search?q=xyzabc");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function health_endpoint_checks_all_services()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'services' => [
                    'database',
                    'cache',
                    'meilisearch',
                ]
            ])
            ->assertJson([
                'status' => 'healthy',
                'services' => [
                    'database' => 'healthy',
                ]
            ]);
    }
}