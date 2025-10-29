## Cómo ejecutar y probar localmente (fuera de Docker)

Por defecto, el archivo `.env` está configurado para el entorno Docker/Sail, usando `DB_HOST=pgsql`.
Para ejecutar y probar localmente (usando `php artisan serve`), defina la variable de entorno `DB_HOST` como `127.0.0.1` antes de iniciar el servidor:

- En PowerShell/Windows:
  ```powershell
  $env:DB_HOST="127.0.0.1"
  php artisan serve
  ```
- En Linux/macOS:
  ```bash
  export DB_HOST=127.0.0.1
  php artisan serve
  ```

Así, la aplicación podrá conectarse al PostgreSQL local normalmente.

## Evidencia de pruebas

Se realizaron pruebas del endpoint de búsqueda utilizando Postman, confirmando que la API responde correctamente con los resultados esperados:

![Prueba del endpoint de búsqueda](docs/postman_test.png)

# Implementación técnica

A continuación se documentan los pasos y decisiones tomadas durante la implementación de la funcionalidad de búsqueda en el proyecto "search-inside-a-book".


## Avances realizados (28/10/2025)

- Se creó el controlador `SearchController` con el método `search`, encargado de leer el archivo JSON del libro, filtrar las páginas por el término buscado y devolver los resultados en formato JSON.
- Se añadió la ruta de API `GET /api/search` en `routes/api.php`, apuntando al método `search` del controlador.
- Se implementó la lógica de búsqueda, extrayendo fragmentos de contexto relevantes y asegurando la codificación UTF-8 en las respuestas.
- Se realizaron pruebas exhaustivas del endpoint `/api/search?query=JavaScript`, resolviendo problemas de codificación y garantizando que la API responde correctamente con resultados esperados.
- Se documentó el proceso de limpieza y validación del archivo JSON para evitar errores de UTF-8.

## Implementação da API de Busca e Página Completa

## Passos realizados

- Corrigido problema de autoload do Laravel: o arquivo `SearchController.php` estava sem a tag de abertura `<?php`, impedindo o reconhecimento da classe pelo Composer. Após adicionar a tag, o endpoint `/api/page/{numero}` passou a funcionar normalmente.
- Testado endpoint `/api/page/2` via Postman, retornando corretamente o conteúdo da página.
- Testado endpoint `/api/search?query=palavra`, retornando resultados (ou vazio, conforme o termo).

## Próximos passos

- [ ] Melhorar documentação dos endpoints e exemplos de uso.
- [ ] Adicionar testes automatizados para os endpoints.
- [ ] (Opcional) Implementar paginação ou filtros avançados na busca.

## Observações

- Atenção: sempre garantir que todos os arquivos PHP tenham a tag de abertura `<?php` para evitar problemas de autoload no Laravel.
- O JSON de dados deve estar limpo e codificado em UTF-8.

# Resumo dos Itens 1 e 2 do Planejamento

## 1. Leitura de Requisitos (README.md)
- **Objetivo:** Implementar uma busca dentro de um livro, exibindo trechos e informações sobre onde a correspondência foi encontrada.
- O usuário pode visualizar a página completa ao selecionar um resultado.
- O arquivo `Eloquent_JavaScript.json` (em `storage/exercise-files/`) contém o texto do livro, página por página.
- O exercício permite foco em backend, frontend, mobile ou abordagem combinada.
- **Documentação:** Decisões, trade-offs, limitações e plano de evolução devem ser registrados.
- **Entrega:** Via Merge Request, funcionando localmente, com instruções claras de execução e testes.

## 2. Configuração do Ambiente
- **Stack:** Laravel 12, PHP 8.3+, Docker, Sail, PostgreSQL, Vite.
- **Passos principais:**
  1. Clonar o fork do repositório.
  2. Copiar `.env.example` para `.env`.
  3. Rodar `composer install`.
  4. Subir o ambiente com `./vendor/bin/sail up -d`.
  5. Gerar a chave da aplicação.
  6. Instalar dependências JS com `./vendor/bin/sail yarn install`.
  7. Rodar `./vendor/bin/sail yarn dev` para desenvolvimento.
  8. Rodar migrations se necessário.
  9. Criar o symlink de storage se for usar arquivos.
  10. Acessar a aplicação em http://localhost:8888.

## Etapa: Implementação da visualização de página completa

- Implementado endpoint `/api/page/{numero}` no backend Laravel, permitindo ao usuário visualizar o conteúdo completo de uma página do livro.
- Corrigido problema de autoload do controller (ausência da tag `<?php` no início do arquivo).
- Testado com sucesso via Postman e curl, retornando corretamente o conteúdo da página solicitada.
- Documentado o fluxo de busca e visualização de página:
  1. Usuário realiza busca por termo usando `/api/search?query=...`.
  2. Recebe lista de resultados com trechos e número da página.
  3. Pode acessar `/api/page/{numero}` para visualizar o texto completo da página.
- Próximos passos: documentar exemplos de uso, adicionar testes automatizados e subir para o GitLab.

---

## Etapa de paginación y visualización de página completa

- Se implementó el endpoint `/api/page/{numero}` en el backend (Laravel) para permitir la visualización del contenido completo de una página del libro.
- El controlador `SearchController` ahora incluye el método `pagina($numero)`, que busca la página solicitada en el archivo JSON y retorna su contenido en formato JSON.
- Se corrigió un problema de autoload (falta de la etiqueta `<?php` en el archivo del controlador), lo que impedía que Laravel reconociera la clase.
- Se probó el endpoint con Postman y curl, confirmando que retorna correctamente el contenido de la página solicitada.
- Esta funcionalidad permite que, tras una búsqueda, el usuario pueda consultar el texto completo de cualquier página encontrada.

---

## Ejemplo de uso de la API

A continuación se muestra una captura de pantalla de la prueba de los endpoints utilizando Postman y el navegador:

![Ejemplo de uso de la API de búsqueda y visualización de página](docs/ejemplo_api_busqueda_pagina.png)

---

## Pruebas automatizadas de la API

- Se crearon pruebas automatizadas en `tests/Feature/SearchTest.php` para validar los endpoints de búsqueda y visualización de página.
- Las pruebas cubren:
  - Búsqueda de un término existente (debe retornar resultados y total mayor que cero).
  - Búsqueda de un término inexistente (debe retornar lista vacía y total igual a cero).
  - Visualización de una página existente (debe retornar el contenido de la página).
  - Visualización de una página inexistente (debe retornar error 404 y mensaje adecuado).
- Todas as provas foram executadas e passaram corretamente:

```bash
vendor\bin\phpunit --filter=SearchTest
```

- Isso garante que a API responde corretamente aos casos esperados e aos erros.

---

## Decisiones técnicas, trade-offs y limitaciones

- **Decisión:** Se optó por una solución backend puro (Laravel) para la búsqueda e visualización, facilitando la integración com qualquer frontend ou cliente.
- **Trade-offs:**
  - La búsqueda se realiza en memoria sobre un archivo JSON. Esto es eficiente para archivos pequeños/medianos, pero no escala bien para libros muy grandes o múltiples libros.
  - No se utiliza base de datos para el texto del libro, lo que simplifica la solución pero limita funcionalidades avanzadas (paginación real, filtros complejos, escalabilidad).
  - El enfoque facilita la portabilidad y la ejecución local, pero não é recomendado para ambientes de produção com grande volume de dados.
- **Limitaciones:**
  - El rendimiento depende del tamaño del archivo JSON e dos recursos do servidor.
  - Não há autenticação, controle de acesso nem logs detalhados de uso.
  - Não há frontend implementado, apenas endpoints de API documentados e testados.

## Proposta de evolução

- Migrar el armazenamento do livro para uma base de dados relacional (PostgreSQL) para permitir buscas mais eficientes e escaláveis.
- Implementar paginação real e filtros avançados na busca.
- Adicionar autenticação e autorização para proteger os endpoints.
- Criar uma interface frontend (web ou mobile) para facilitar a experiência do usuário.
- Adicionar logs e métricas de uso para monitoramento e auditoria.

---

*Próximos passos: detalhar as decisões técnicas, trade-offs e plano de evolução do projeto.*

## Evidencias de pruebas de los endpoints (29/10/2025)

### 1. Endpoint de búsqueda paginada

**Solicitud:**
```
curl -G --data-urlencode "query=JavaScript" --data-urlencode "page=1" --data-urlencode "per_page=3" http://localhost:8888/api/search
```
**Respuesta:**
```
{
  "resultados": [
    { "pagina": 2, "contexto": "EloquentJavaScript 3rdedition Marijn Haverbeke" },
    { "pagina": 3, "contexto": "The third edition of Eloquent JavaScript was made possible by 325 fina" },
    { "pagina": 4, "contexto": " . . . . . . . . .  4 What is JavaScript? . . . . . . . . . . . . . . " }
  ],
  "total": 187,
  "pagina_atual": 1,
  "por_pagina": 3
}
```

### 2. Endpoint de página específica

**Solicitud:**
```
curl http://localhost:8888/api/page/2
```
**Respuesta:**
```
{
  "id": 1,
  "page": 2,
  "text_content": "EloquentJavaScript 3rdedition Marijn Haverbeke",
  ...
}
```

Ambos endpoints respondieron correctamente, comprobando el funcionamiento de la API integrada con la base de datos Docker.

### Captura de pantalla de las pruebas de los endpoints

![Pruebas de los endpoints de búsqueda y página](docs/teste_api_search.png)

La imagen anterior muestra la terminal ejecutando los comandos curl para los endpoints `/api/search` y `/api/page/2`, comprobando el funcionamiento correcto de la API.

---
