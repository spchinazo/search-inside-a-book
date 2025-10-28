<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * Testa a busca por um termo existente.
     */
    public function test_busca_termo_existente()
    {
        $response = $this->getJson('/api/search?query=JavaScript');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'resultados',
                     'total',
                 ]);
        $data = $response->json();
        $this->assertIsArray($data['resultados']);
        $this->assertGreaterThan(0, $data['total']);
    }

    /**
     * Testa a busca por um termo inexistente.
     */
    public function test_busca_termo_inexistente()
    {
        $response = $this->getJson('/api/search?query=palavranãoexiste');
        $response->assertStatus(200)
                 ->assertJson([
                     'resultados' => [],
                     'total' => 0,
                 ]);
    }

    /**
     * Testa a visualização de uma página existente.
     */
    public function test_visualizacao_pagina_existente()
    {
        $response = $this->getJson('/api/page/2');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'page',
                     'text_content',
                 ]);
    }

    /**
     * Testa a visualização de uma página inexistente.
     */
    public function test_visualizacao_pagina_inexistente()
    {
        $response = $this->getJson('/api/page/99999');
        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Página no encontrada.'
                 ]);
    }
}
