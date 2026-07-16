<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Alerta de escaneo QR</title>
</head>
<body style="margin:0; padding:0; background:#eef4f6; font-family:Arial, Helvetica, sans-serif; color:#172026;">
    @php
        $isBiker = $tag->type === \App\Models\SmartTag::TYPE_BIKER;
        $accent = $isBiker ? '#ff6b35' : '#0e8585';
        $soft = $isBiker ? '#fff1e8' : '#e8f8f5';
        $mapImage = 'https://staticmap.openstreetmap.de/staticmap.php?center='.$latitude.','.$longitude.'&zoom=16&size=640x360&markers='.$latitude.','.$longitude.',red-pushpin';
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef4f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; overflow:hidden; border-radius:18px; background:#ffffff; box-shadow:0 18px 44px rgba(23,32,38,.14);">
                    <tr>
                        <td style="padding:0; background:linear-gradient(135deg, #172026 0%, #24343b 58%, {{ $accent }} 58%, {{ $accent }} 100%);">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:28px 28px 24px;">
                                        <div style="display:inline-block; padding:7px 10px; border-radius:999px; background:rgba(255,255,255,.14); color:#ffffff; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">
                                            {{ $isBiker ? 'Biker emergency ID' : 'Dog Tag QR' }}
                                        </div>
                                        <h1 style="margin:16px 0 6px; color:#ffffff; font-size:30px; line-height:1.08;">Alerta de escaneo QR</h1>
                                        <p style="margin:0; color:rgba(255,255,255,.82); font-size:16px;">
                                            Se escaneo el perfil de <strong style="color:#ffffff;">{{ $tag->display_name }}</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 28px 10px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:14px; border-radius:14px; background:{{ $soft }}; border:1px solid rgba(23,32,38,.08);">
                                        <div style="font-size:12px; color:#66717f; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Folio</div>
                                        <div style="margin-top:4px; font-size:24px; font-weight:800; color:#172026;">{{ $tag->tag_code ?: 'Sin folio' }}</div>
                                    </td>
                                    <td width="12"></td>
                                    <td style="padding:14px; border-radius:14px; background:#f7f9fb; border:1px solid rgba(23,32,38,.08);">
                                        <div style="font-size:12px; color:#66717f; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Tipo de sangre</div>
                                        <div style="margin-top:4px; font-size:24px; font-weight:800; color:#c62828;">{{ $tag->blood_type ?: 'N/D' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 28px;">
                            <img src="{{ $mapImage }}" alt="Mapa del escaneo" width="624" style="display:block; width:100%; max-width:624px; border:0; border-radius:16px; background:#d9e3e6;">
                            <p style="margin:10px 0 0; color:#66717f; font-size:13px;">Ubicacion aproximada reportada por el dispositivo que escaneo el QR.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:4px 28px 18px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate; border-spacing:0 8px;">
                                <tr>
                                    <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Producto:</strong> {{ $tag->type_label }}</td>
                                </tr>
                                @if ($isBiker)
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Donador:</strong> {{ $tag->is_blood_donor ? 'Si' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Moto / vehiculo:</strong> {{ $tag->vehicle ?: 'No capturado' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Placas:</strong> {{ $tag->motorcycle_plate ?: 'No capturadas' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Club:</strong> {{ $tag->club_name ?: 'Independiente' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Especie:</strong> {{ $tag->pet_species ?: 'Mascota' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Raza:</strong> {{ $tag->pet_breed ?: 'No capturada' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Alergias:</strong> {{ $tag->allergies ?: 'Sin alergias registradas' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;"><strong>Notas:</strong> {{ $tag->medical_notes ?: 'Sin notas capturadas' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 18px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-radius:16px; background:#ffffff; border:1px solid rgba(23,32,38,.1);">
                                <tr>
                                    <td style="padding:18px;">
                                        <div style="font-size:13px; color:#66717f; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Contactos de emergencia</div>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:10px; border-collapse:separate; border-spacing:0 8px;">
                                            <tr>
                                                <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;">
                                                    <strong>Principal:</strong> {{ $tag->owner_name ?: 'No capturado' }}<br>
                                                    Telefono: {{ $tag->owner_phone ?: 'No capturado' }}<br>
                                                    Correo: {{ $tag->owner_email ?: 'No capturado' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;">
                                                    <strong>Secundario:</strong> {{ $tag->secondary_contact_name ?: 'No capturado' }}<br>
                                                    Telefono: {{ $tag->secondary_contact_phone ?: 'No capturado' }}<br>
                                                    Correo: {{ $tag->secondary_contact_email ?: 'No capturado' }}
                                                </td>
                                            </tr>
                                            @if (! $isBiker)
                                                <tr>
                                                    <td style="padding:12px 14px; border-radius:12px; background:#f7f9fb;">
                                                        <strong>Veterinaria:</strong> {{ $tag->vet_name ?: 'No capturado' }}<br>
                                                        Telefono: {{ $tag->vet_phone ?: 'No capturado' }}<br>
                                                        Correo: {{ $tag->vet_email ?: 'No capturado' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-radius:16px; background:#172026; color:#ffffff;">
                                <tr>
                                    <td style="padding:18px;">
                                        <div style="font-size:13px; color:rgba(255,255,255,.62); font-weight:700; text-transform:uppercase; letter-spacing:.06em;">Coordenadas del escaneo</div>
                                        <p style="margin:8px 0 14px; font-size:15px;">
                                            Latitud: {{ $latitude }}<br>
                                            Longitud: {{ $longitude }}<br>
                                            @if ($accuracy)
                                                Precision aproximada: {{ round($accuracy) }} metros<br>
                                            @endif
                                            Hora: {{ $scannedAt->format('Y-m-d H:i:s') }}<br>
                                            IP: {{ $ip }}
                                        </p>
                                        <a href="{{ $mapsUrl }}" style="display:inline-block; padding:12px 16px; background:{{ $accent }}; color:#ffffff; text-decoration:none; border-radius:10px; font-weight:800;">Abrir ubicacion en mapa</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 26px;">
                            <p style="margin:0; color:#66717f; font-size:13px;">
                                La ubicacion depende del permiso otorgado por la persona que escaneo el QR y de la precision del dispositivo. Este aviso no sustituye servicios medicos, veterinarios ni de emergencia.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
