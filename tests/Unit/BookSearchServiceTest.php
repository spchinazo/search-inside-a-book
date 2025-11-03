<?php

namespace Tests\Unit;

use App\Services\BookSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchServiceTest extends TestCase
{
    private BookSearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchService = new BookSearchService();
    }

    public function test_can_instantiate_service()
    {
        $this->assertInstanceOf(BookSearchService::class, $this->searchService);
    }

    public function test_search_returns_empty_array_for_empty_query()
    {
        $results = $this->searchService->search('');
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_search_returns_results_for_valid_query()
    {
        $results = $this->searchService->search('the DOM');
        
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        
        // Check structure of first result
        $firstResult = $results[0];
        $this->assertArrayHasKey('page', $firstResult);
        $this->assertArrayHasKey('snippet', $firstResult);
        $this->assertArrayHasKey('position', $firstResult);
        $this->assertArrayHasKey('highlighted_snippet', $firstResult);
        $this->assertArrayHasKey('match_count_in_page', $firstResult);
    }

    public function test_search_highlights_query_in_snippet()
    {
        $results = $this->searchService->search('DOM');
        
        if (!empty($results)) {
            $firstResult = $results[0];
            $this->assertStringContainsString('<mark>', $firstResult['highlighted_snippet']);
            $this->assertStringContainsString('</mark>', $firstResult['highlighted_snippet']);
        }
    }

    public function test_search_respects_limit()
    {
        $limit = 5;
        $results = $this->searchService->search('JavaScript', $limit);
        
        $this->assertLessThanOrEqual($limit, count($results));
    }

    public function test_search_is_case_insensitive()
    {
        $resultsLower = $this->searchService->search('javascript');
        $resultsUpper = $this->searchService->search('JAVASCRIPT');
        $resultsMixed = $this->searchService->search('JavaScript');
        
        $this->assertNotEmpty($resultsLower);
        $this->assertNotEmpty($resultsUpper);
        $this->assertNotEmpty($resultsMixed);
    }

    public function test_get_page_returns_valid_page()
    {
        // Page 2 exists in the test data
        $page = $this->searchService->getPage(2);
        
        $this->assertIsArray($page);
        $this->assertArrayHasKey('page', $page);
        $this->assertArrayHasKey('text_content', $page);
        $this->assertEquals(2, $page['page']);
    }

    public function test_get_page_returns_null_for_invalid_page()
    {
        $page = $this->searchService->getPage(999999);
        $this->assertNull($page);
    }

    public function test_get_stats_returns_valid_structure()
    {
        $stats = $this->searchService->getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_pages', $stats);
        $this->assertArrayHasKey('first_page', $stats);
        $this->assertArrayHasKey('last_page', $stats);
        $this->assertGreaterThan(0, $stats['total_pages']);
    }

    public function test_search_includes_context_around_match()
    {
        $results = $this->searchService->search('function');
        
        if (!empty($results)) {
            $firstResult = $results[0];
            // Snippet should be longer than just the search term
            $this->assertGreaterThan(8, strlen($firstResult['snippet']));
        }
    }

    public function test_search_with_special_characters()
    {
        // Test that special regex characters don't break the search
        $results = $this->searchService->search('(function)');
        $this->assertIsArray($results);
    }
}
