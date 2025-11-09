<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Page;

class PageTest extends TestCase
{
    /** @test */
    public function it_extracts_snippet_with_context_and_highlight()
    {
        $text = 'Laravel é um framework PHP moderno. O Laravel facilita o desenvolvimento.';
        $term = 'Laravel';
        $snippet = Page::extractSnippet($text, $term, 10);
        $this->assertStringContainsString('<mark>Laravel</mark>', $snippet);
        $this->assertTrue(strlen($snippet) <= strlen($text));
    }
    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $page = new Page([
            'page' => 10,
            'text_content' => 'Conteúdo de teste.'
        ]);

        $this->assertEquals(10, $page->page);
        $this->assertEquals('Conteúdo de teste.', $page->text_content);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $page = new Page();
        $this->assertEquals(['page', 'text_content'], $page->getFillable());
    }
}
