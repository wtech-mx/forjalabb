@extends('layouts.app')
@section('title', 'Configuracion | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Administracion del sistema</div>
                <h1 class="fw-bold mt-2 mb-0">Configuracion</h1>
                <p class="text-secondary mb-0 mt-2">Respaldos y restauracion de la base de datos.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>No se pudo completar la operacion.</strong>
                <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <article class="panel-card h-100">
                    <div class="settings-icon bg-success-subtle text-success"><i class="bi bi-database-down"></i></div>
                    <h2 class="h4 fw-bold mt-3">Descargar respaldo</h2>
                    <p class="text-secondary">Genera una copia SQL de la base actual <strong>{{ $databaseName }}</strong>. Puedes guardarla o llevarla a otra instalacion.</p>
                    <form method="POST" action="{{ route('admin.settings.database.download') }}">
                        @csrf
                        <button class="btn btn-dark" type="submit"><i class="bi bi-download me-2"></i>Descargar base de datos</button>
                    </form>
                </article>
            </div>

            <div class="col-lg-6">
                <article class="panel-card h-100 border-danger-subtle">
                    <div class="settings-icon bg-danger-subtle text-danger"><i class="bi bi-database-up"></i></div>
                    <h2 class="h4 fw-bold mt-3">Restaurar respaldo</h2>
                    <p class="text-secondary">Carga una base exportada desde el servidor para reemplazar la base de esta instalacion.</p>
                    <div class="alert alert-warning small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Esta accion reemplaza los datos actuales. Antes se creara un respaldo automatico de seguridad.</div>
                    <form method="POST" action="{{ route('admin.settings.database.restore') }}" enctype="multipart/form-data" id="restore-database-form">
                        @csrf
                        <label class="form-label fw-bold" for="database_backup">Archivo SQL</label>
                        <input class="form-control mb-3" id="database_backup" type="file" name="database_backup" accept=".sql,application/sql,text/plain" required>
                        <label class="form-label fw-bold" for="confirmation">Escribe <code>RESTAURAR</code> para confirmar</label>
                        <input class="form-control mb-3" id="confirmation" name="confirmation" autocomplete="off" required placeholder="RESTAURAR">
                        <button class="btn btn-danger" type="submit"><i class="bi bi-arrow-repeat me-2"></i>Restaurar base de datos</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>
<style>
    .settings-icon{display:grid;place-items:center;width:3.25rem;height:3.25rem;border-radius:1rem;font-size:1.45rem}
</style>
@push('scripts')
<script>
    document.getElementById('restore-database-form')?.addEventListener('submit', (event) => {
        if (!window.confirm('Se reemplazaran todos los datos actuales. ¿Deseas continuar?')) event.preventDefault();
    });
</script>
@endpush
@endsection
