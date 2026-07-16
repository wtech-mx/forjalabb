<script>
(() => {
    const status = document.querySelector('[data-scan-status]');
    const trigger = document.querySelector('[data-scan-trigger]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const endpoint = @json(route('tags.scan', $tag->token));
    const sessionKey = @json('scan-alert-sent-'.$tag->token);
    let locating = false;

    const setStatus = (message, tone = 'info') => {
        if (!status) return;
        status.dataset.tone = tone;
        const text = status.querySelector('span');
        if (text) text.textContent = message;
    };

    const setButton = (message, disabled = false) => {
        if (!trigger) return;
        trigger.textContent = message;
        trigger.disabled = disabled;
    };

    const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);
    if (!window.isSecureContext && !isLocalhost) {
        setStatus('Para obtener GPS preciso abre este perfil con HTTPS.', 'warning');
        setButton('GPS requiere HTTPS', true);
        return;
    }

    if (!navigator.geolocation) {
        setStatus('Este navegador no permite obtener ubicacion. Usa llamada o WhatsApp para avisar.', 'warning');
        setButton('GPS no disponible', true);
        return;
    }

    if (sessionStorage.getItem(sessionKey) === '1') {
        setStatus('La alerta de esta visita ya fue enviada.', 'success');
        setButton('Alerta enviada', true);
        return;
    }

    const sendPosition = async (position) => {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
            }),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'No se pudo enviar la alerta automatica.');
        }

        sessionStorage.setItem(sessionKey, '1');
        const meters = Math.round(position.coords.accuracy || 0);
        setStatus(`${data.message || 'Alerta enviada.'} Precision aproximada: ${meters} m.`, 'success');
        setButton('Alerta enviada', true);
    };

    const requestPreciseLocation = () => {
        if (locating) return;
        locating = true;
        setButton('Buscando GPS...', true);
        setStatus('Buscando ubicacion GPS de alta precision. Mantente en exterior o cerca de una ventana si es posible.', 'info');

        let bestPosition = null;
        let watchId = null;
        let finished = false;

        const finish = async () => {
            if (finished) return;
            finished = true;
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);

            if (!bestPosition) {
                locating = false;
                setStatus('No se obtuvo ubicacion. Revisa permisos de GPS e intenta otra vez.', 'warning');
                setButton('Intentar de nuevo', false);
                return;
            }

            try {
                setStatus('Enviando alerta con la mejor ubicacion obtenida...', 'info');
                await sendPosition(bestPosition);
            } catch (error) {
                locating = false;
                setStatus(error.message || 'No se pudo enviar la alerta. Usa llamada o WhatsApp.', 'warning');
                setButton('Intentar de nuevo', false);
            }
        };

        watchId = navigator.geolocation.watchPosition((position) => {
            if (!bestPosition || position.coords.accuracy < bestPosition.coords.accuracy) {
                bestPosition = position;
                setStatus(`GPS encontrado. Mejorando precision: ${Math.round(position.coords.accuracy)} m...`, 'info');
            }

            if (position.coords.accuracy <= 30) {
                finish();
            }
        }, (error) => {
            locating = false;
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
            const denied = error.code === error.PERMISSION_DENIED;
            setStatus(denied ? 'Permiso de ubicacion denegado. Activa GPS/permisos o usa llamada/WhatsApp.' : 'No se pudo obtener GPS. Intenta de nuevo o usa los botones de contacto.', 'warning');
            setButton('Intentar de nuevo', false);
        }, {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0,
        });

        window.setTimeout(finish, 15000);
    };

    trigger?.addEventListener('click', requestPreciseLocation);

    navigator.permissions?.query({ name: 'geolocation' }).then((permission) => {
        if (permission.state === 'granted') {
            requestPreciseLocation();
        } else if (permission.state === 'denied') {
            setStatus('GPS bloqueado para este sitio. Activa el permiso de ubicacion en el navegador.', 'warning');
            setButton('Permiso bloqueado', true);
        } else {
            setStatus('Toca “Enviar ubicacion” para autorizar GPS y mandar la alerta con mapa.', 'info');
            setButton('Enviar ubicacion GPS', false);
        }
    }).catch(() => {
        setStatus('Toca “Enviar ubicacion” para autorizar GPS y mandar la alerta con mapa.', 'info');
        setButton('Enviar ubicacion GPS', false);
    });
})();
</script>
