@extends('layouts.app')
@section('title', 'Configuracion | ForjaLab')
@section('content')
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div>
                <div class="eyebrow">Administracion del sistema</div>
                <h1 class="fw-bold mt-2 mb-0">Configuracion</h1>
                <p class="text-secondary mb-0 mt-2">Respaldos, restauracion y conexiones del sistema.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <article class="panel-card whatsapp-settings-card">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-3">
                            <div class="settings-icon whatsapp-icon"><i class="bi bi-whatsapp"></i></div>
                            <div><h2 class="h4 fw-bold mb-1">WhatsApp Business</h2><p class="text-secondary mb-0">Escanea el QR una sola vez para enviar mensajes desde los pedidos.</p></div>
                        </div>
                        <span class="whatsapp-state is-loading" data-whatsapp-state><i class="bi bi-arrow-repeat"></i> Consultando</span>
                    </div>
                    <div class="whatsapp-connect mt-4" data-whatsapp-panel data-status-url="{{ route('admin.settings.whatsapp.status') }}">
                        <div class="whatsapp-qr-wrap d-none" data-whatsapp-qr-wrap><img data-whatsapp-qr alt="Codigo QR para conectar WhatsApp Business"><small>WhatsApp Business → Dispositivos vinculados → Vincular dispositivo</small></div>
                        <div>
                            <div class="alert alert-light border mb-3" data-whatsapp-message>Iniciando consulta del servicio...</div>
                            <ol class="small text-secondary mb-3"><li>Inicia el servicio con <code>npm start</code> dentro de <code>whatsapp-service</code>.</li><li>Escanea el QR desde el telefono que tiene WhatsApp Business.</li><li>Manten el servicio ejecutandose para poder enviar mensajes.</li></ol>
                            <form class="d-none" method="POST" action="{{ route('admin.settings.whatsapp.logout') }}" data-whatsapp-logout data-confirm="La sesión dejará de enviar mensajes hasta escanear un nuevo QR." data-confirm-title="¿Desconectar WhatsApp?" data-confirm-button="Sí, desconectar">@csrf<button class="btn btn-outline-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Desconectar cuenta</button></form>
                        </div>
                    </div>
                    <div class="alert alert-warning small mt-3 mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Esta conexion usa WhatsApp Web y no es la API oficial. Evita envios masivos y mensajes no solicitados.</div>
                </article>
            </div>
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
                    <form method="POST" action="{{ route('admin.settings.database.restore') }}" enctype="multipart/form-data" id="restore-database-form" data-confirm="Se reemplazarán todos los datos actuales y antes se generará un respaldo de seguridad." data-confirm-title="¿Restaurar la base de datos?" data-confirm-button="Sí, restaurar">
                        @csrf
                        <label class="form-label fw-bold" for="database_backup">Archivo SQL</label>
                        <input class="form-control mb-3" id="database_backup" type="file" name="database_backup" accept=".sql,application/sql,text/plain" required>
                        <label class="form-label fw-bold" for="confirmation">Escribe <code>RESTAURAR</code> para confirmar</label>
                        <input class="form-control mb-3" id="confirmation" name="confirmation" autocomplete="off" required placeholder="RESTAURAR">
                        <button class="btn btn-danger" type="submit"><i class="bi bi-arrow-repeat me-2"></i>Restaurar base de datos</button>
                        <small class="d-block text-secondary mt-2"><i class="bi bi-hourglass-split me-1"></i>La importación puede tardar varios minutos. No cierres esta ventana ni vuelvas a presionar el botón.</small>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>
<style>
    .settings-icon{display:grid;place-items:center;width:3.25rem;height:3.25rem;border-radius:1rem;font-size:1.45rem}
    .whatsapp-settings-card{border-top:4px solid #25d366;background:linear-gradient(135deg,#fff,rgba(37,211,102,.07))}.whatsapp-icon{color:#fff;background:#25d366}.whatsapp-state{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .75rem;font-size:.75rem;font-weight:850;border-radius:999px}.whatsapp-state.is-loading,.whatsapp-state.is-offline{color:#705d42;background:#f3eadb}.whatsapp-state.is-qr{color:#8a6500;background:#fff0bf}.whatsapp-state.is-ready{color:#17652b;background:#d8f2df}.whatsapp-state.is-error{color:#8f2530;background:#f8d7da}.whatsapp-connect{display:grid;grid-template-columns:360px minmax(0,1fr);align-items:center;gap:2rem}.whatsapp-qr-wrap{display:grid;justify-items:center;gap:.6rem;padding:1rem;background:#fff;border:1px solid var(--line);border-radius:1rem}.whatsapp-qr-wrap img{width:min(100%,320px)}.whatsapp-qr-wrap small{text-align:center;color:var(--muted)}@media(max-width:767.98px){.whatsapp-connect{grid-template-columns:1fr}.whatsapp-qr-wrap{max-width:380px;margin:auto}}
</style>
@push('scripts')
<script>
    const whatsappPanel = document.querySelector('[data-whatsapp-panel]');
    if (whatsappPanel) {
        const badge=document.querySelector('[data-whatsapp-state]'), message=document.querySelector('[data-whatsapp-message]'), qrWrap=document.querySelector('[data-whatsapp-qr-wrap]'), qr=document.querySelector('[data-whatsapp-qr]'), logout=document.querySelector('[data-whatsapp-logout]');
        const labels={offline:['is-offline','bi-cloud-slash','Servicio apagado'],starting:['is-loading','bi-arrow-repeat','Iniciando'],qr:['is-qr','bi-qr-code','Esperando QR'],authenticated:['is-loading','bi-shield-check','Autenticando'],ready:['is-ready','bi-check-circle-fill','Conectado'],disconnected:['is-offline','bi-wifi-off','Desconectado'],error:['is-error','bi-exclamation-circle-fill','Error']};
        const refresh=async()=>{try{const response=await fetch(whatsappPanel.dataset.statusUrl,{headers:{Accept:'application/json'}});const data=await response.json();const status=labels[data.state]||labels.error;badge.className=`whatsapp-state ${status[0]}`;badge.innerHTML=`<i class="bi ${status[1]}"></i> ${status[2]}`;qrWrap.classList.toggle('d-none',!data.qr);logout.classList.toggle('d-none',!data.connected);if(data.qr)qr.src=data.qr;if(data.connected)message.innerHTML=`<strong>${data.account?.name||'WhatsApp Business'}</strong><br>Numero conectado: ${data.account?.phone||'disponible'}`;else if(data.state==='qr')message.textContent='Escanea el codigo para conectar la cuenta.';else message.textContent=data.error||'Esperando que WhatsApp quede disponible...';}catch(error){badge.className='whatsapp-state is-error';badge.innerHTML='<i class="bi bi-exclamation-circle-fill"></i> Sin respuesta';message.textContent='No se pudo consultar el servicio de WhatsApp.';}};
        refresh(); window.setInterval(refresh,5000);
    }
</script>
@endpush
@endsection
