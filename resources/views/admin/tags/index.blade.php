@extends('layouts.app')

@section('title', 'Tags | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">QR/NFC</div>
                    <h1 class="fw-bold mt-2 mb-0">Tags registrados</h1>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning" href="{{ route('admin.tags.invitations.create') }}"><i class="bi bi-send-fill me-2"></i>Enviar formulario al cliente</a>
                    <a class="btn btn-dark" href="{{ route('admin.tags.create', ['type' => 'biker']) }}">Nuevo Biker Tag</a>
                    <a class="btn btn-outline-dark" href="{{ route('admin.tags.create', ['type' => 'dog']) }}">Nuevo Dog Tag</a>
                </div>
            </div>

            <div class="panel-card">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a class="btn btn-sm {{ request('type') ? 'btn-outline-dark' : 'btn-dark' }}" href="{{ route('admin.tags.index') }}">Todos</a>
                    <a class="btn btn-sm {{ request('type') === 'biker' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ route('admin.tags.index', ['type' => 'biker']) }}">Biker Tags</a>
                    <a class="btn btn-sm {{ request('type') === 'dog' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ route('admin.tags.index', ['type' => 'dog']) }}">Dog Tags</a>
                </div>
                @include('admin.tags.partials.table', ['tags' => $tags])
                <div class="mt-3">{{ $tags->links() }}</div>
            </div>
        </div>
    </section>
@endsection
