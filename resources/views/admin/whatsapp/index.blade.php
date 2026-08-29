@extends('layouts.app')
@section('title', 'WhatsApp Web | ForjaLab')
@section('content')
<section class="admin-section whatsapp-module-section">
    <div class="container">
        <div class="admin-header whatsapp-module-heading">
            <div><div class="eyebrow">Centro de conversaciones</div><h1 class="fw-bold mt-2 mb-0">WhatsApp Web</h1><p class="text-secondary mb-0 mt-2">Consulta y responde mensajes sin salir de ForjaLab.</p></div>
            <div class="wa-connection" data-wa-connection><i class="bi bi-arrow-repeat"></i><span>Conectando</span></div>
        </div>

        <div class="wa-app" data-wa-app
            data-status-url="{{ route('admin.settings.whatsapp.status') }}"
            data-chats-url="{{ route('admin.whatsapp.chats') }}"
            data-messages-url="{{ route('admin.whatsapp.messages') }}"
            data-send-url="{{ route('admin.whatsapp.send') }}">
            <aside class="wa-sidebar">
                <header><span class="wa-brand"><i class="bi bi-whatsapp"></i></span><div><strong>Conversaciones</strong><small data-wa-chat-count>Cargando chats...</small></div><button type="button" title="Actualizar" data-wa-refresh><i class="bi bi-arrow-clockwise"></i></button></header>
                <div class="wa-search"><i class="bi bi-search"></i><input type="search" placeholder="Buscar conversación" data-wa-search></div>
                <div class="wa-chat-list" data-wa-chat-list><div class="wa-list-state"><span class="spinner-border spinner-border-sm"></span>Cargando conversaciones...</div></div>
            </aside>

            <main class="wa-conversation">
                <div class="wa-empty" data-wa-empty><span><i class="bi bi-chat-dots-fill"></i></span><h2>WhatsApp de ForjaLab</h2><p>Selecciona una conversación para consultar el historial y responder.</p></div>
                <div class="wa-thread d-none" data-wa-thread>
                    <header class="wa-thread-header"><button class="wa-back" type="button" data-wa-back><i class="bi bi-arrow-left"></i></button><span class="wa-avatar" data-wa-avatar>W</span><div><strong data-wa-name>Contacto</strong><small><i class="bi bi-circle-fill"></i> Conversación de WhatsApp</small></div><button type="button" title="Actualizar mensajes" data-wa-thread-refresh><i class="bi bi-arrow-clockwise"></i></button></header>
                    <div class="wa-messages" data-wa-messages></div>
                    @can('orders.manage')
                        <form class="wa-composer" data-wa-composer><textarea rows="1" maxlength="4096" placeholder="Escribe un mensaje" aria-label="Mensaje" data-wa-message required></textarea><button type="submit" title="Enviar"><i class="bi bi-send-fill"></i></button></form>
                    @else
                        <div class="wa-readonly"><i class="bi bi-eye"></i> Tu rol permite consultar, pero no enviar mensajes.</div>
                    @endcan
                </div>
            </main>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const app = document.querySelector('[data-wa-app]');
    if (!app) return;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const list = app.querySelector('[data-wa-chat-list]'), messages = app.querySelector('[data-wa-messages]'), empty = app.querySelector('[data-wa-empty]'), thread = app.querySelector('[data-wa-thread]');
    const search = app.querySelector('[data-wa-search]'), connection = document.querySelector('[data-wa-connection]'), count = app.querySelector('[data-wa-chat-count]');
    let chats = [], active = null, loadingMessages = false;
    const initials = name => (name || 'W').trim().slice(0, 1).toUpperCase();
    const time = timestamp => timestamp ? new Intl.DateTimeFormat('es-MX', {hour:'2-digit',minute:'2-digit'}).format(new Date(timestamp * 1000)) : '';
    const dayTime = timestamp => timestamp ? new Intl.DateTimeFormat('es-MX', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}).format(new Date(timestamp * 1000)) : '';
    const api = async (url, options = {}) => { const response = await fetch(url, {headers:{Accept:'application/json','X-CSRF-TOKEN':csrf,...options.headers},...options}); const data = await response.json(); if (!response.ok) throw Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'No se pudo completar la solicitud.'); return data; };
    const showError = error => window.Swal.fire({title:'WhatsApp no disponible',text:error.message,icon:'error',confirmButtonText:'Entendido',customClass:{popup:'forjalab-swal',confirmButton:'btn btn-dark px-4'},buttonsStyling:false});

    const renderChats = () => {
        const query = search.value.trim().toLowerCase();
        const filtered = chats.filter(chat => `${chat.name} ${chat.phone || ''} ${chat.last_message || ''}`.toLowerCase().includes(query));
        list.replaceChildren(); count.textContent = `${chats.length} conversaciones`;
        if (!filtered.length) { const state=document.createElement('div'); state.className='wa-list-state'; state.textContent=query?'No hay coincidencias.':'No hay conversaciones disponibles.'; list.append(state); return; }
        filtered.forEach(chat => {
            const button=document.createElement('button'); button.type='button'; button.className=`wa-chat ${active?.id===chat.id?'active':''}`;
            const avatar=document.createElement('span'); avatar.className='wa-avatar'; avatar.textContent=initials(chat.name);
            const copy=document.createElement('span'); const top=document.createElement('span'); top.className='wa-chat-top'; const name=document.createElement('strong'); name.textContent=chat.name; const stamp=document.createElement('time'); stamp.textContent=time(chat.timestamp); top.append(name,stamp);
            const preview=document.createElement('small'); preview.textContent=`${chat.last_from_me?'Tú: ':''}${chat.last_message || 'Sin mensajes recientes'}`; copy.append(top,preview); button.append(avatar,copy);
            if(chat.unread){const unread=document.createElement('b');unread.textContent=chat.unread;button.append(unread)}
            button.addEventListener('click',()=>openChat(chat)); list.append(button);
        });
    };
    const loadChats = async (quiet = false) => { try { if(!quiet) list.innerHTML='<div class="wa-list-state"><span class="spinner-border spinner-border-sm"></span>Cargando conversaciones...</div>'; const data=await api(app.dataset.chatsUrl); chats=data.chats || []; renderChats(); } catch(error) { list.innerHTML=`<div class="wa-list-state text-danger"><i class="bi bi-exclamation-circle"></i>${error.message}</div>`; if(!quiet) showError(error); } };
    const renderMessages = items => { messages.replaceChildren(); items.forEach(item=>{const row=document.createElement('div');row.className=`wa-message-row ${item.from_me?'out':'in'}`;const bubble=document.createElement('article');if(item.has_media){const media=document.createElement('small');media.className='wa-media-label';media.innerHTML='<i class="bi bi-paperclip"></i> Archivo multimedia';bubble.append(media)} const body=document.createElement('p');body.textContent=item.body || (item.has_media?'':'Mensaje no compatible');const meta=document.createElement('small');meta.textContent=dayTime(item.timestamp);if(item.from_me){const ack=document.createElement('i');ack.className=`bi ${item.ack>=3?'bi-check2-all':'bi-check2'}`;meta.append(' ',ack)}bubble.append(body,meta);row.append(bubble);messages.append(row)}); messages.scrollTop=messages.scrollHeight; };
    const loadMessages = async (quiet=false) => { if(!active || loadingMessages)return;loadingMessages=true;try{if(!quiet)messages.innerHTML='<div class="wa-message-loading"><span class="spinner-border spinner-border-sm"></span>Cargando mensajes...</div>';const data=await api(app.dataset.messagesUrl,{method:'POST',body:JSON.stringify({chat_id:active.id}),headers:{'Content-Type':'application/json'}});renderMessages(data.messages||[])}catch(error){if(!quiet)showError(error)}finally{loadingMessages=false}};
    const openChat = chat => { active=chat; app.classList.add('has-active-chat'); empty.classList.add('d-none'); thread.classList.remove('d-none'); app.querySelector('[data-wa-name]').textContent=chat.name; app.querySelector('[data-wa-avatar]').textContent=initials(chat.name); renderChats(); loadMessages(); };
    const checkConnection = async () => { try { const data=await api(app.dataset.statusUrl);connection.className=`wa-connection ${data.connected?'online':'offline'}`;connection.innerHTML=data.connected?'<i class="bi bi-check-circle-fill"></i><span>Conectado</span>':'<i class="bi bi-wifi-off"></i><span>Desconectado</span>';if(data.connected&&!chats.length)loadChats(); }catch{connection.className='wa-connection offline';connection.innerHTML='<i class="bi bi-wifi-off"></i><span>Sin servicio</span>'} };
    search.addEventListener('input',renderChats); app.querySelector('[data-wa-refresh]').addEventListener('click',()=>loadChats()); app.querySelector('[data-wa-thread-refresh]').addEventListener('click',()=>loadMessages()); app.querySelector('[data-wa-back]').addEventListener('click',()=>app.classList.remove('has-active-chat'));
    app.querySelector('[data-wa-composer]')?.addEventListener('submit',async event=>{event.preventDefault();const input=app.querySelector('[data-wa-message]'),value=input.value.trim();if(!value||!active)return;const button=event.currentTarget.querySelector('button');button.disabled=true;try{await api(app.dataset.sendUrl,{method:'POST',body:JSON.stringify({chat_id:active.id,message:value}),headers:{'Content-Type':'application/json'}});input.value='';await loadMessages(true);await loadChats(true)}catch(error){showError(error)}finally{button.disabled=false;input.focus()}});
    checkConnection(); window.setInterval(()=>{checkConnection();if(active)loadMessages(true)},5000);
});
</script>
@endpush
