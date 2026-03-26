<?php

namespace Tests\Unit;

use App\Services\BookSearch;
use App\Services\BookContent;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_matches_with_snippet_and_page(): void
    {
        $service = $this->app->make(BookSearch::class);

        $results = $service->search('javascript', 5, 1);

        $this->assertNotEmpty($results['data']);
        $first = $results['data'][0];

        $this->assertArrayHasKey('page', $first);
        $this->assertArrayHasKey('page_id', $first);
        $this->assertArrayHasKey('snippet', $first);
        $this->assertIsString($first['snippet']);
        $this->assertGreaterThan(0, $results['total']);
    }

    public function test_it_limits_matches_per_page(): void
    {
        $service = $this->app->make(BookSearch::class);

        $results = $service->search('the', 10, 1);
        $pages = array_column($results['data'], 'page');

        // If limited to one per page, number of unique pages should equal total results.
        $this->assertSame(count($results['data']), count(array_unique($pages)));
    }
}
