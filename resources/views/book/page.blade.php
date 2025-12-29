<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página {{ $page }} - Libro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-5xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <a href="/" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M15.78 5.72a.75.75 0 010 1.06L10.56 12l5.22 5.22a.75.75 0 11-1.06 1.06l-5.75-5.75a.75.75 0 010-1.06l5.75-5.75a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
            Volver
        </a>
        <div class="text-sm text-gray-600">Página {{ $page }}</div>
    </div>

    @if(!$exists)
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            No se pudo cargar la página {{ $page }}.
        </div>
    @else
        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white shadow">
            <img src="{{ route('book.page.image', ['page' => $page]) }}" alt="Página {{ $page }}" class="w-full h-auto"/>
        </div>
    @endif
</div>
</body>
</html>
