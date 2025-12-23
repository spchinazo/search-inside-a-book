<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página {{ $pageData['page'] }} - Eloquent JavaScript</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('search.index') }}">Buscar</a></li>
                <li class="breadcrumb-item active">Página {{ $pageData['page'] }}</li>
            </ol>
        </nav>

        <h1 class="mb-4">Página {{ $pageData['page'] }}</h1>

        <div class="card">
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap; font-family: monospace;">{{ $pageData['text_content'] }}</pre>
            </div>
        </div>

        <div class="mt-3">
            @if($pageData['page'] > 1)
                <a href="{{ route('page.show', $pageData['page'] - 1) }}" class="btn btn-secondary">Página Anterior</a>
            @endif
            @if($pageData['page'] < 699) <!-- Assuming max page -->
                <a href="{{ route('page.show', $pageData['page'] + 1) }}" class="btn btn-secondary">Página Siguiente</a>
            @endif
        </div>
    </div>
</body>
</html>