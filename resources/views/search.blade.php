
@extends('layouts.dashboard')

@section('page_title', 'Buscar en el libro')
@section('breadcrumb')
    <li class="breadcrumb-item active">Buscar en el libro</li>
@endsection

@section('content')
 
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <form method="GET" action="{{ route('search.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control" placeholder="Ingrese el término de búsqueda" value="{{ old('query', $query) }}" required>
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>

                @if(isset($results))
                    @if(count($results) > 0)
                        <h5>Resultados:</h5>
                        <div class="list-group mb-3">
                            @foreach($results as $result)
                                <div class="list-group-item">
                                    <strong>Página {{ $result['page'] }}:</strong>
                                    <div>{!! $result['context'] !!}</div>
                                    <a href="{{ route('search.show', $result['page']) }}" class="btn btn-link btn-sm p-0">Ver página completa</a>
                                </div>
                            @endforeach
                        </div>
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
        </div>
    </div>
</section>
@endsection
