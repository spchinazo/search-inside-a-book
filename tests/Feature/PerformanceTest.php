<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use MeiliSearch\Client;

class PerformanceTest extends TestCase
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
        
        // Configure Meilisearch index with filterable attributes
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
    public function search_endpoint_responds_within_acceptable_time(): void
    {
        $book = Book::factory()->create();
        
        // Create 100 pages to simulate real book
        BookPage::factory()->count(100)->create([
            'book_id' => $book->id,
            'text_content' => 'This is sample content for performance testing with various keywords.',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(3); // Wait for Meilisearch to index
        
        $start = microtime(true);
        $response = $this->getJson("/api/books/{$book->id}/search?q=sample");
        $duration = (microtime(true) - $start) * 1000; // Convert to ms
        
        $response->assertStatus(200);
        
        // Search should respond within 500ms (includes Meilisearch query)
        $this->assertLessThan(500, $duration, 
            "Search took {$duration}ms, expected < 500ms"
        );
        
        echo "\n Search performance: {$duration}ms\n";
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cached_books_list_responds_quickly(): void
    {
        Book::factory()->count(10)->create();
        
        // Prime the cache
        $this->getJson('/api/books');
        
        // Measure cached response
        $start = microtime(true);
        $response = $this->getJson('/api/books');
        $duration = (microtime(true) - $start) * 1000;
        
        $response->assertStatus(200);
        
        // Cached response should be under 50ms
        $this->assertLessThan(50, $duration, 
            "Cached books list took {$duration}ms, expected < 50ms"
        );
        
        echo "\n Cached books list performance: {$duration}ms\n";
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cached_page_content_responds_quickly(): void
    {
        $book = Book::factory()->create();
        $page = BookPage::factory()->create(['book_id' => $book->id]);
        
        // Prime cache
        $this->getJson("/api/pages/{$page->id}");
        
        // Measure cached response
        $start = microtime(true);
        $response = $this->getJson("/api/pages/{$page->id}");
        $duration = (microtime(true) - $start) * 1000;
        
        $response->assertStatus(200);
        
        // Cached page should be under 30ms
        $this->assertLessThan(30, $duration,
            "Cached page took {$duration}ms, expected < 30ms"
        );
        
        echo "\n Cached page content performance: {$duration}ms\n";
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function health_check_responds_quickly(): void
    {
        $start = microtime(true);
        $response = $this->getJson('/api/health');
        $duration = (microtime(true) - $start) * 1000;
        
        $this->assertContains($response->status(), [200, 503]);
        
        // Health check should be very fast
        $this->assertLessThan(100, $duration,
            "Health check took {$duration}ms, expected < 100ms"
        );
        
        echo "\n Health check performance: {$duration}ms\n";
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_with_pagination_performs_consistently(): void
    {
        $book = Book::factory()->create();
        
        // Create enough pages for multiple pages of results (25+ pages)
        for ($i = 1; $i <= 30; $i++) {
            BookPage::factory()->create([
                'book_id' => $book->id,
                'page_number' => $i,
                'text_content' => "Page {$i} contains the search term test multiple times test test.",
            ]);
        }
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(3);
        
        // Test first page
        $start1 = microtime(true);
        $response1 = $this->getJson("/api/books/{$book->id}/search?q=test&page=1");
        $duration1 = (microtime(true) - $start1) * 1000;
        
        // Test second page
        $start2 = microtime(true);
        $response2 = $this->getJson("/api/books/{$book->id}/search?q=test&page=2");
        $duration2 = (microtime(true) - $start2) * 1000;
        
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Both pages should perform similarly (within 100ms of each other)
        $difference = abs($duration1 - $duration2);
        $this->assertLessThan(100, $difference,
            "Page 1 ({$duration1}ms) and Page 2 ({$duration2}ms) performance differs by {$difference}ms"
        );
        
        echo "\n Search with pagination performance: {$duration1}ms, {$duration2}ms (diff: {$difference}ms)\n";
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cache_significantly_improves_performance(): void
    {
        Book::factory()->count(5)->create();
        
        Cache::forget('all_books_list');
        
        // Measure without cache (first request)
        $start1 = microtime(true);
        $response1 = $this->getJson('/api/books');
        $uncachedDuration = (microtime(true) - $start1) * 1000;
        
        // Measure with cache (second request)
        $start2 = microtime(true);
        $response2 = $this->getJson('/api/books');
        $cachedDuration = (microtime(true) - $start2) * 1000;
        
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Cached should be at least 2x faster
        $speedup = $uncachedDuration / max($cachedDuration, 0.001); // Avoid division by zero
        $this->assertGreaterThan(2, $speedup,
            "Cache speedup: {$speedup}x (uncached: {$uncachedDuration}ms, cached: {$cachedDuration}ms)"
        );
        
        echo "\n Cache speedup: " . number_format($speedup, 1) . "x (uncached: {$uncachedDuration}ms, cached: {$cachedDuration}ms)\n";
    }
}