<?php

namespace Tests\Unit;

use App\Models\Page;
use App\Services\BookSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchServiceTest extends TestCase
{
    use RefreshDatabase; // Para ejecutar migraciones y limpiar la base de datos en cada prueba

    private BookSearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchService = new BookSearchService; // Inicializa el servicio de búsqueda

        // Crear datos de prueba
        $this->seedTestData();
    }

    private function seedTestData(): void
    {
        Page::create([
            'page_number' => 1,
            'text_content' => 'Esta es una página de prueba sobre funciones JavaScript y closures.',
        ]);

        Page::create([
            'page_number' => 2,
            'text_content' => 'Otra página discutiendo el Document Object Model o DOM.',
        ]);

        Page::create([
            'page_number' => 3,
            'text_content' => 'Una página sobre arrays, objetos y estructuras de datos.',
        ]);
    }

    public function test_search_returns_matching_pages(): void
    {
        $results = $this->searchService->search('JavaScript');

        $this->assertCount(1, $results);
        $this->assertEquals(1, $results->first()['page_number']);
    }

    public function test_search_is_case_insensitive(): void
    {
        $results = $this->searchService->search('javascript');

        $this->assertCount(1, $results);
    }

    public function test_search_returns_snippet_with_highlight(): void
    {
        $results = $this->searchService->search('DOM');

        $snippet = $results->first()['snippet'];

        $this->assertStringContainsString('<mark>', $snippet);
        $this->assertStringContainsString('</mark>', $snippet);
        $this->assertStringContainsString('DOM', $snippet);
    }

    public function test_search_orders_results_by_page_number(): void
    {
        // Buscar algo que aparece en múltiples páginas
        $results = $this->searchService->search('página');

        $pageNumbers = $results->pluck('page_number')->toArray();

        // Debería estar ordenado: [1, 2, 3]
        $this->assertEquals([1, 2, 3], $pageNumbers);
    }

    public function test_empty_search_returns_empty_collection(): void
    {
        $results = $this->searchService->search('');

        $this->assertCount(0, $results);
    }

    public function test_get_page_returns_correct_page(): void
    {
        $page = $this->searchService->getPage(2);

        $this->assertNotNull($page);
        $this->assertEquals(2, $page->page_number);
        $this->assertStringContainsString('DOM', $page->text_content);
    }

    public function test_get_page_returns_null_for_nonexistent_page(): void
    {
        $page = $this->searchService->getPage(999);

        $this->assertNull($page);
    }

    public function test_count_matches_returns_correct_count(): void
    {
        $count = $this->searchService->countMatches('página');

        $this->assertEquals(3, $count);
    }
}
