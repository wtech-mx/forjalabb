const http = require('http');
const QRCode = require('qrcode');
const { Client, LocalAuth } = require('whatsapp-web.js');

const host = process.env.WHATSAPP_HOST || (process.env.PORT ? '0.0.0.0' : '127.0.0.1');
const port = Number(process.env.PORT || process.env.WHATSAPP_PORT || 3210);
const apiToken = process.env.WHATSAPP_API_TOKEN || 'forjalab-local-whatsapp';
let state = 'starting', qrImage = null, account = null, lastError = null;

const client = new Client({
    authStrategy: new LocalAuth({ dataPath: './session' }),
    puppeteer: { headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'] },
});

client.on('qr', async (qr) => { state = 'qr'; account = null; lastError = null; qrImage = await QRCode.toDataURL(qr, { width: 360, margin: 2 }); });
client.on('authenticated', () => { state = 'authenticated'; qrImage = null; });
client.on('ready', () => {
    state = 'ready'; qrImage = null; lastError = null;
    account = client.info ? { name: client.info.pushname || null, phone: client.info.wid?.user || null } : null;
});
client.on('auth_failure', (message) => { state = 'error'; lastError = message; });
client.on('disconnected', (reason) => { state = 'disconnected'; account = null; qrImage = null; lastError = String(reason || 'Sesion desconectada'); });
client.initialize().catch((error) => { state = 'error'; lastError = error.message; });

const json = (response, status, payload) => {
    response.writeHead(status, { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store' });
    response.end(JSON.stringify(payload));
};
const readBody = (request) => new Promise((resolve, reject) => {
    let raw = '';
    request.on('data', (chunk) => { raw += chunk; if (raw.length > 262144) reject(new Error('Solicitud demasiado grande.')); });
    request.on('end', () => { try { resolve(raw ? JSON.parse(raw) : {}); } catch { reject(new Error('JSON invalido.')); } });
    request.on('error', reject);
});

const server = http.createServer(async (request, response) => {
    if (request.headers.authorization !== `Bearer ${apiToken}`) return json(response, 401, { message: 'No autorizado.' });
    if (request.method === 'GET' && request.url === '/status') return json(response, 200, { state, connected: state === 'ready', qr: qrImage, account, error: lastError });

    if (request.method === 'GET' && request.url === '/chats') {
        if (state !== 'ready') return json(response, 409, { message: 'WhatsApp no esta conectado.' });
        try {
            const chats = (await client.pupPage.evaluate(() => window.require('WAWebCollections').Chat.getModelsArray().map((chat) => {
                const chatMessages = chat.msgs?.getModelsArray?.() || [];
                const lastMessage = chatMessages[chatMessages.length - 1];
                return {
                    id: chat.id?._serialized,
                    name: chat.formattedTitle || chat.name || chat.contact?.pushname || chat.id?.user || 'Contacto',
                    phone: chat.id?.server === 'c.us' ? chat.id.user : null,
                    timestamp: chat.t || lastMessage?.t || 0,
                    unread: chat.unreadCount || 0,
                    last_message: lastMessage?.body || (lastMessage?.type && lastMessage.type !== 'chat' ? `[${lastMessage.type}]` : ''),
                    last_from_me: Boolean(lastMessage?.id?.fromMe),
                    is_group: chat.id?.server === 'g.us',
                };
            })))
                .filter((chat) => chat.id)
                .filter((chat) => chat.timestamp > 0)
                .sort((a, b) => b.timestamp - a.timestamp)
                .slice(0, 250);
            return json(response, 200, { chats });
        } catch (error) { return json(response, 500, { message: error.message || 'No se pudieron cargar las conversaciones.' }); }
    }

    if (request.method === 'POST' && request.url === '/messages') {
        if (state !== 'ready') return json(response, 409, { message: 'WhatsApp no esta conectado.' });
        try {
            const body = await readBody(request);
            const chatId = String(body.chat_id || '');
            if (!chatId || chatId.length > 100) return json(response, 422, { message: 'Conversacion invalida.' });
            const result = await client.pupPage.evaluate(({ chatId, limit }) => {
                const wid = window.require('WAWebWidFactory').createWid(chatId);
                const chat = window.require('WAWebCollections').Chat.get(wid);
                if (!chat) return null;
                const items = (chat.msgs?.getModelsArray?.() || []).slice(-limit).map((message) => ({
                    id: message.id?._serialized || `${message.t}-${message.id?.fromMe}`,
                    body: message.body || '',
                    from_me: Boolean(message.id?.fromMe),
                    timestamp: message.t || 0,
                    type: message.type || 'chat',
                    has_media: Boolean(message.isMedia || message.isMMS),
                    ack: message.ack ?? null,
                }));
                return { chat: { id: chatId, name: chat.formattedTitle || chat.name || chat.contact?.pushname || chat.id?.user || 'Contacto' }, messages: items };
            }, { chatId, limit: Math.min(Number(body.limit) || 50, 100) });
            if (!result) return json(response, 404, { message: 'No se encontro la conversacion.' });
            return json(response, 200, result);
        } catch (error) { return json(response, 500, { message: error.message || 'No se pudieron cargar los mensajes.' }); }
    }

    if (request.method === 'POST' && request.url === '/chat/send') {
        if (state !== 'ready') return json(response, 409, { message: 'WhatsApp no esta conectado.' });
        try {
            const body = await readBody(request);
            const chatId = String(body.chat_id || '');
            const message = String(body.message || '').trim();
            if (!chatId || chatId.length > 100 || !message || message.length > 4096) return json(response, 422, { message: 'Conversacion o mensaje invalido.' });
            const sent = await client.sendMessage(chatId, message);
            return json(response, 200, { sent: true, id: sent?.id?._serialized || null, timestamp: sent?.timestamp || Math.floor(Date.now() / 1000) });
        } catch (error) { return json(response, 500, { message: error.message || 'No se pudo enviar el mensaje.' }); }
    }

    if (request.method === 'POST' && request.url === '/send') {
        if (state !== 'ready') return json(response, 409, { message: 'WhatsApp no esta conectado.' });
        try {
            const body = await readBody(request);
            let phone = String(body.phone || '').replace(/\D/g, '');
            const message = String(body.message || '').trim();
            if (phone.length === 10) phone = `52${phone}`;
            if (phone.length < 11 || !message || message.length > 4096) return json(response, 422, { message: 'Telefono o mensaje invalido.' });
            const numberId = await client.getNumberId(phone);
            if (!numberId?._serialized) return json(response, 422, { message: 'El numero no tiene WhatsApp.' });
            // WhatsApp ahora puede devolver un identificador @lid en lugar de @c.us.
            // Enviar al ID resuelto evita el error "No LID for user".
            const sent = await client.sendMessage(numberId._serialized, message);
            return json(response, 200, { sent: true, id: sent?.id?._serialized || null });
        } catch (error) { return json(response, 500, { message: error.message || 'No se pudo enviar el mensaje.' }); }
    }

    if (request.method === 'POST' && request.url === '/logout') {
        try { await client.logout(); state = 'disconnected'; account = null; qrImage = null; return json(response, 200, { disconnected: true }); }
        catch (error) { return json(response, 500, { message: error.message || 'No se pudo cerrar la sesion.' }); }
    }
    return json(response, 404, { message: 'Ruta no encontrada.' });
});

server.listen(port, host, () => console.log(`ForjaLab WhatsApp en http://${host}:${port}`));
