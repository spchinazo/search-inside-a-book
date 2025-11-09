<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Page;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Cria páginas de teste
        Page::factory()->create(['page' => 1, 'text_content' => 'Introducción a JavaScript.']);
        Page::factory()->create(['page' => 2, 'text_content' => 'Eloquent JavaScript 3rd edition.']);
        Page::factory()->create(['page' => 3, 'text_content' => 'Sin resultados relevantes.']);
    }

    public function test_busca_api_retorna_resultados_para_termo_existente()
    {
        $response = $this->getJson('/api/search?query=JavaScript');
        $response->assertStatus(200)
            ->assertJsonFragment(['pagina' => 1])
            ->assertJsonFragment(['pagina' => 2])
            ->assertJsonStructure([
                'resultados',
                'total',
                'pagina_atual',
                'por_pagina',
            ]);
    }

    public function test_busca_api_retorna_vazio_para_termo_inexistente()
    {
        $response = $this->getJson('/api/search?query=Python');
        $response->assertStatus(200)
            ->assertJson([
                'resultados' => [],
            ]);
    }

    public function test_visualizacao_pagina_existente()
    {
        $response = $this->getJson('/api/page/2');
        $response->assertStatus(200)
            ->assertJsonFragment(['page' => 2])
            ->assertJsonFragment(['text_content' => 'Eloquent JavaScript 3rd edition.']);
    }

    public function test_visualizacao_pagina_inexistente()
    {
        $response = $this->getJson('/api/page/99');
        $response->assertStatus(404);
    }
}
