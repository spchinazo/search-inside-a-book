<?php

namespace Tests\Unit;

use App\Services\BookContent;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookContentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_loads_pages_as_plain_text(): void
    {
        $service = $this->app->make(BookContent::class);
        $pages = $service->allPages();

        $this->assertNotEmpty($pages);
        $this->assertIsString($pages[0]);
        $this->assertStringContainsString('Eloquent', $pages[0]);
    }

    public function test_it_returns_single_page_with_metadata(): void
    {
        $service = $this->app->make(BookContent::class);
        $page = $service->getPage(2);

        $this->assertNotNull($page);
        $this->assertSame(2, $page['page']);
        $this->assertIsString($page['content']);
        $this->assertNotSame('', $page['content']);
    }
}
