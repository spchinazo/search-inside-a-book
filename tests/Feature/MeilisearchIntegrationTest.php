<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MeiliSearch\Client;

class MeilisearchIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_is_accessible(): void
    {
        $health = $this->client->health();
        
        $this->assertArrayHasKey('status', $health);
        $this->assertEquals('available', $health['status']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_indexes_pages_correctly(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->count(10)->create([
            'book_id' => $book->id,
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        $stats = $index->stats();
        
        $this->assertArrayHasKey('numberOfDocuments', $stats);
        $this->assertGreaterThanOrEqual(10, $stats['numberOfDocuments']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_has_correct_filterable_attributes(): void
    {
        $this->artisan('scout:sync-index-settings');
        sleep(1);
        
        $index = $this->client->index('book_pages');
        $settings = $index->getFilterableAttributes();
        
        $this->assertContains('book_id', $settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_has_correct_searchable_attributes(): void
    {
        $this->artisan('scout:sync-index-settings');
        sleep(1);
        
        $index = $this->client->index('book_pages');
        $settings = $index->getSearchableAttributes();
        
        $this->assertContains('text_content', $settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_highlighting_works_correctly(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'The quick brown fox jumps over the lazy dog.',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        
        $results = $index->search('fox', [
            'attributesToHighlight' => ['text_content'],
            'highlightPreTag' => '<em>',
            'highlightPostTag' => '</em>',
        ]);
        
        // Convert SearchResult object to array
        $hits = $results->getHits();
        
        $this->assertNotEmpty($hits);
        $this->assertArrayHasKey('_formatted', $hits[0]);
        $this->assertStringContainsString('<em>fox</em>', $hits[0]['_formatted']['text_content']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_filters_by_book_id_correctly(): void
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book1->id,
            'text_content' => 'Content for book 1',
        ]);
        
        BookPage::factory()->create([
            'book_id' => $book2->id,
            'text_content' => 'Content for book 2',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        
        // Configure filterable attributes before testing filter
        $index = $this->client->index('book_pages');
        $task = $index->updateFilterableAttributes(['book_id']);
        $this->client->waitForTask($task['taskUid'], 5000);
        
        sleep(2);
        
        $results = $index->search('Content', [
            'filter' => "book_id = {$book1->id}",
        ]);
        
        $hits = $results->getHits();
        
        $this->assertCount(1, $hits);
        $this->assertEquals($book1->id, $hits[0]['book_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_typo_tolerance_is_enabled(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'JavaScript is a programming language.',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        
        // Search with typo: "JavaScrpt" instead of "JavaScript"
        $results = $index->search('JavaScrpt');
        
        $hits = $results->getHits();
        
        $this->assertNotEmpty($hits, 'Typo tolerance should find results');
        $this->assertStringContainsString('JavaScript', $hits[0]['text_content']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_returns_ranking_scores(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'JavaScript JavaScript JavaScript is important.',
        ]);
        
        BookPage::factory()->create([
            'book_id' => $book->id,
            'text_content' => 'JavaScript is mentioned once here.',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        
        $results = $index->search('JavaScript', [
            'showRankingScore' => true,
        ]);
        
        $hits = $results->getHits();
        
        $this->assertNotEmpty($hits);
        $this->assertArrayHasKey('_rankingScore', $hits[0]);
        $this->assertIsFloat($hits[0]['_rankingScore']);
        $this->assertGreaterThan(0, $hits[0]['_rankingScore']);
        $this->assertLessThanOrEqual(1, $hits[0]['_rankingScore']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_sorts_by_relevance(): void
    {
        $book = Book::factory()->create();
        
        // Page with exact match in multiple places
        BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 1,
            'text_content' => 'JavaScript JavaScript JavaScript programming language JavaScript',
        ]);
        
        // Page with only one mention and different context
        BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 2,
            'text_content' => 'Python is a programming language. Java is different from JavaScript.',
        ]);
        
        // Page with no mention
        BookPage::factory()->create([
            'book_id' => $book->id,
            'page_number' => 3,
            'text_content' => 'Ruby and Python are popular programming languages.',
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        
        $results = $index->search('JavaScript', [
            'showRankingScore' => true,
        ]);
        
        $hits = $results->getHits();
        
        // Should return at least 2 results (pages 1 and 2)
        $this->assertGreaterThanOrEqual(2, count($hits));
        
        // Verify that all results contain JavaScript
        foreach ($hits as $hit) {
            $this->assertStringContainsString('JavaScript', $hit['text_content']);
        }
        
        // Verify scores are descending (most relevant first)
        if (count($hits) >= 2) {
            for ($i = 0; $i < count($hits) - 1; $i++) {
                $currentScore = $hits[$i]['_rankingScore'];
                $nextScore = $hits[$i + 1]['_rankingScore'];
                
                $this->assertGreaterThanOrEqual($nextScore, $currentScore, 
                    "Results should be sorted by relevance (descending scores). " .
                    "Hit {$i} score: {$currentScore}, Hit " . ($i + 1) . " score: {$nextScore}"
                );
            }
        }
        
        // Page 1 (with 4 mentions) should be in the results
        $pageNumbers = array_column($hits, 'page_number');
        $this->assertContains(1, $pageNumbers, 'Page with most mentions should appear in results');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function meilisearch_handles_empty_queries_gracefully(): void
    {
        $book = Book::factory()->create();
        
        BookPage::factory()->count(5)->create([
            'book_id' => $book->id,
        ]);
        
        $this->artisan('scout:import', ['model' => 'App\Models\BookPage']);
        sleep(2);
        
        $index = $this->client->index('book_pages');
        
        // Empty query should return all documents
        $results = $index->search('', [
            'limit' => 10,
        ]);
        
        $hits = $results->getHits();
        
        $this->assertGreaterThanOrEqual(5, count($hits));
    }

    protected function tearDown(): void
    {
        try {
            $this->client->index('book_pages')->delete();
        } catch (\Exception $e) {
            // Index may not exist
        }
        
        parent::tearDown();
    }
}