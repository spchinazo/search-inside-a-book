<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Inside a Book</title>
    @vite(['resources/js/app.js'])
</head>
<body>
<div class="page">
    <header class="header">
        <div>
            <p class="eyebrow">Publica.la exercise</p>
            <h1>Search inside a book</h1>
            <p class="subhead">Busca un término, revisa los snippets y abre la página completa.</p>
        </div>
    </header>

    <main class="panel">
        <form id="search-form" class="search-form">
            <label for="search-term">Término de búsqueda</label>
            <div class="search-row">
                <input id="search-term" name="q" type="text" placeholder="Ej: javascript" required minlength="2" />
                <button type="submit">Buscar</button>
            </div>
            <p class="help">Búsqueda case-insensitive sobre el contenido del libro.</p>
        </form>

        <div id="status" class="status">Ingresa un término para comenzar.</div>

        <div id="favorites-block" class="favorites-wrap" hidden>
            <div class="chip-group">
                <span class="badge-label">Favoritos:</span>
                <div id="fav-badges-top" class="badge-list"></div>
            </div>
        </div>

        <div id="results-block" class="results-wrap" hidden>
            <div class="results-header">
                <h2 class="section-title">Resultados</h2>
            </div>
            <div class="chips">
                <div class="chip-group">
                    <span class="badge-label">Vistas:</span>
                    <div id="seen-badges" class="badge-list"></div>
                </div>
            </div>
            <p id="results-heading" class="results-heading" hidden></p>
            <ul id="results-list" class="results-list" aria-live="polite"></ul>
            <ul id="results-grid" class="results-grid" aria-live="polite" hidden></ul>
            <div id="pager" class="pager" hidden>
                <div class="view-controls">
                    <button type="button" class="icon-toggle" data-view="grid" title="Ver en mosaico">⧉</button>
                    <button type="button" class="icon-toggle" data-view="list" title="Ver en lista">☰</button>
                </div>
                <button type="button" id="pager-prev" class="ghost">Anterior</button>
                <span id="pager-info" class="pager-info"></span>
                <button type="button" id="pager-next" class="ghost">Siguiente</button>
            </div>
        </div>

        <section id="page-viewer" class="page-viewer" hidden>
            <div class="viewer-header">
                <div>
                    <p class="eyebrow" id="viewer-meta"></p>
                    <h2 id="viewer-title">Page</h2>
                </div>
                <button id="close-viewer" type="button" class="ghost">Close</button>
            </div>
            <pre id="viewer-content"></pre>
        </section>
    </main>
</div>
</body>
</html>
