@extends('layouts.app')

@section('title', 'Buscar en Eloquent JavaScript')

@section('content')
<div class="container">
    <h1 class="mb-4">Buscar en Eloquent JavaScript</h1>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" placeholder="Ingresa tu búsqueda..." value="{{ $query }}" required>
            <button type="submit">Buscar</button>
        </div>
    </form>

    @if($query)
        <h2>Resultados para "{{ $query }}"</h2>
        @if($results && count($results) > 0)
            <p class="text-muted">{{ count($results) }} resultados encontrados</p>
            <div class="list-group">
                @foreach($results as $result)
                    <a href="{{ route('page.show', $result['page']) }}" class="list-group-item">
                        <div class="mb-1"><strong>Página {{ $result['page'] }}</strong></div>
                        <div class="mb-1">{!! $result['snippet'] !!}</div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-muted">No se encontraron resultados.</p>
        @endif
    @endif
</div>
@endsection