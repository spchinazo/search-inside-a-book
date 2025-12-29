<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/js/app.js'])
    @livewireStyles

</head>
<body class="bg-white">
<div class="flex-center position-ref full-height">
    <div class="content">
        @livewire('book-search')
    </div> 
    
</div>

    @livewireScripts

</body>
</html>
