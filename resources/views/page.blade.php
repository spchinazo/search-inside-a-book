@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Página {{ $page['page'] ?? '' }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('search.index') }}" class="btn btn-secondary float-right">&larr; Volver a la búsqueda</a>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <pre style="white-space: pre-wrap;">{{ $page['text_content'] ?? 'Contenido no disponible.' }}</pre>
            </div>
        </div>
    </div>
</section>
@endsection
