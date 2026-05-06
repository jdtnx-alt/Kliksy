<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Kliksy</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.07);">

                    {{-- HEADER AZUL --}}
                    <tr>
                        <td style="background:#2563eb;padding:36px 40px 32px;text-align:center;">
                            <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;background:white;border-radius:14px;margin-bottom:16px;">
                                <span style="font-size:24px;font-weight:900;color:#2563eb;">K</span>
                            </div>
                            <h1 style="margin:0;font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">Kliksy</h1>
                            <p style="margin:6px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">Tu marketplace de servicios a domicilio</p>
                        </td>
                    </tr>

                    {{-- CUERPO --}}
                    <tr>
                        <td style="padding:40px 40px 32px;">

                            <h2 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111827;">
                                ¡Hola, {{ $nombreUsuario }}! 👋
                            </h2>

                            <p style="margin:0 0 16px;font-size:15px;color:#4b5563;line-height:1.7;">
                                Nos alegra mucho que hayas llegado a <strong style="color:#2563eb;">Kliksy</strong>. A partir de hoy tienes acceso a los mejores profesionales de servicios a domicilio cerca de ti.
                            </p>

                            <p style="margin:0 0 32px;font-size:15px;color:#4b5563;line-height:1.7;">
                                Ya seas cliente buscando un profesional o un profesional ofreciendo tus servicios, estamos aquí para conectarte con las personas correctas.
                            </p>

                            {{-- BOTÓN --}}
                            <div style="text-align:center;">
                                <a href="{{ $urlApp }}"
                                    style="display:inline-block;background:#2563eb;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:12px;letter-spacing:0.2px;">
                                    Explorar servicios
                                </a>
                            </div>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background:#f9fafb;padding:20px 40px;border-top:1px solid #f3f4f6;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.6;">
                                Este correo fue enviado por <strong style="color:#6b7280;">Kliksy</strong> porque te registraste en nuestra plataforma.<br>
                                Florencia, Caquetá, Colombia.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>