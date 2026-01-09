<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Buscar Dentro de un Libro' }}</title>
        @vite(['resources/js/app.js'])
    </head>
    <body class="bg-gray-100 dark:bg-gray-900">
        {{ $slot }}
    </body>
</html>
