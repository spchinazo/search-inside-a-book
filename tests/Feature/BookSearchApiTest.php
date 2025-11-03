<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchApiTest extends TestCase
{
    public function test_search_endpoint_requires_query_parameter()
    {
        $response = $this->getJson('/api/book/search');
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'q'
            ]
        ]);
    }

    public function test_search_endpoint_requires_minimum_query_length()
    {
        $response = $this->getJson('/api/book/search?q=a');
        
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_search_endpoint_returns_results_for_valid_query()
    {
        $response = $this->getJson('/api/book/search?q=the+DOM');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'query',
            'total_results',
            'results' => [
                '*' => [
                    'page',
                    'snippet',
                    'position',
                    'highlighted_snippet',
                    'match_count_in_page',
                ]
            ],
            'search_time_ms',
        ]);
        
        $response->assertJson([
            'success' => true,
            'query' => 'the DOM',
        ]);
    }

    public function test_search_endpoint_respects_limit_parameter()
    {
        $limit = 5;
        $response = $this->getJson("/api/book/search?q=JavaScript&limit={$limit}");
        
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertLessThanOrEqual($limit, count($data['results']));
    }

    public function test_search_endpoint_returns_empty_results_for_nonexistent_term()
    {
        $response = $this->getJson('/api/book/search?q=xyzabc123notfound');
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_results' => 0,
            'results' => [],
        ]);
    }

    public function test_page_endpoint_returns_valid_page()
    {
        $response = $this->getJson('/api/book/page/2');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'page' => [
                'page',
                'text_content',
            ]
        ]);
        
        $response->assertJson([
            'success' => true,
            'page' => [
                'page' => 2,
            ]
        ]);
    }

    public function test_page_endpoint_returns_404_for_invalid_page()
    {
        $response = $this->getJson('/api/book/page/999999');
        
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Page not found',
        ]);
    }

    public function test_stats_endpoint_returns_book_statistics()
    {
        $response = $this->getJson('/api/book/stats');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'stats' => [
                'total_pages',
                'first_page',
                'last_page',
            ]
        ]);
        
        $response->assertJson([
            'success' => true,
        ]);
        
        $data = $response->json();
        $this->assertGreaterThan(0, $data['stats']['total_pages']);
    }

    public function test_search_endpoint_handles_special_characters()
    {
        $response = $this->getJson('/api/book/search?q=' . urlencode('(function)'));
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_search_endpoint_rejects_too_long_queries()
    {
        $longQuery = str_repeat('a', 201);
        $response = $this->getJson('/api/book/search?q=' . urlencode($longQuery));
        
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_search_endpoint_includes_performance_metrics()
    {
        $response = $this->getJson('/api/book/search?q=JavaScript');
        
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('search_time_ms', $data);
        $this->assertIsNumeric($data['search_time_ms']);
    }

    public function test_web_route_returns_search_view()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewIs('book-search');
    }
}
