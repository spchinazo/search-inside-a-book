@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Buscar en el libro</h1>
    <form method="GET" action="{{ route('search.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="query" class="form-control" placeholder="Ingrese el término de búsqueda" value="{{ old('query', $query) }}" required>
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    @if(isset($results))
        @if(count($results) > 0)
            <h5>Resultados:</h5>
            <ul class="list-group mb-3">
                @foreach($results as $result)
                    <li class="list-group-item">
                        <strong>Página {{ $result['page'] }}:</strong>
                        <div>{{ $result['context'] }}</div>
                        <a href="{{ route('search.show', $result['page']) }}" class="btn btn-link btn-sm">Ver página completa</a>
                    </li>
                @endforeach
            </ul>
            @if($pagination && $pagination['last_page'] > 1)
                <nav>
                    <ul class="pagination">
                        @for($i = 1; $i <= $pagination['last_page']; $i++)
                            <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                <a class="page-link" href="?query={{ urlencode($query) }}&page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            @endif
        @else
            <div class="alert alert-warning">No se encontraron resultados.</div>
        @endif
    @endif
</div>
@endsection
