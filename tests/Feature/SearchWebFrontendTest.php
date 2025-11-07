<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Page;

class SearchWebFrontendTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Page::factory()->create(['page' => 1, 'text_content' => 'Introducción a JavaScript.']);
        Page::factory()->create(['page' => 2, 'text_content' => 'Eloquent JavaScript 3rd edition.']);
    }

    public function test_formulario_busca_renderiza()
    {
        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Buscar en el libro')
            ->assertSee('Buscar');
    }

    public function test_busca_exibe_resultados()
    {
        $response = $this->get('/?query=JavaScript');
        $response->assertStatus(200)
            ->assertSee('Página 1')
            ->assertSee('Página 2')
            ->assertSee('Ver página completa');
    }

    public function test_busca_sem_resultados_exibe_mensagem()
    {
        $response = $this->get('/?query=Python');
        $response->assertStatus(200)
            ->assertSee('No se encontraron resultados');
    }

    public function test_visualizacao_pagina_completa()
    {
        $response = $this->get('/page/1');
        $response->assertStatus(200)
            ->assertSee('Introducción a JavaScript.');
    }

    public function test_visualizacao_pagina_inexistente()
    {
        $response = $this->get('/page/99');
        $response->assertStatus(404);
    }
}
