@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('search.index') }}" class="btn btn-secondary mb-3">&larr; Voltar para busca</a>
    <div class="card">
        <div class="card-header">
            Página {{ $page['page'] ?? '' }}
        </div>
        <div class="card-body">
            <pre style="white-space: pre-wrap;">{{ $page['text_content'] ?? 'Conteúdo não disponível.' }}</pre>
        </div>
    </div>
</div>
@endsection
