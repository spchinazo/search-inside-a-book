<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_results_for_query(): void
    {
        $response = $this->getJson('/api/search?q=javascript');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['page', 'page_id', 'snippet'],
            ],
            'meta' => ['term', 'count', 'total', 'page', 'per_page', 'total_pages'],
        ]);

        $this->assertNotEmpty($response->json('data'));
    }

    public function test_it_applies_limits_and_validation(): void
    {
        $response = $this->getJson('/api/search?q=js&per_page=2&page=1&max_per_page=1');
        $response->assertOk();

        $this->assertCount(2, $response->json('data'));
        $this->assertEquals(1, $response->json('meta.page'));
        $this->assertEquals(2, $response->json('meta.per_page'));

        $invalid = $this->getJson('/api/search?q=j');
        $invalid->assertStatus(422);
    }
}
