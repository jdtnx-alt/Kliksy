<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 16px; }
        .wrapper { max-width: 520px; margin: 0 auto; }
        .card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #2563eb; padding: 28px 24px; text-align: center; }
        .header h1 { color: white; font-size: 20px; margin-bottom: 6px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 13px; }
        .body { padding: 24px; }
        .row { margin-bottom: 18px; }
        .label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; margin-bottom: 3px; }
        .value { font-size: 15px; color: #111827; }
        .badge { display: inline-block; background: #fef9c3; color: #92400e; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: bold; }
        .divider { border: none; border-top: 1px solid #f3f4f6; margin: 4px 0 20px; }
        .cta { display: block; background: #2563eb; color: white !important; text-decoration: none; text-align: center; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: bold; margin-top: 8px; }
        .footer { text-align: center; padding: 16px 24px; color: #9ca3af; font-size: 11px; border-top: 1px solid #f3f4f6; line-height: 1.6; }
        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 14px; }
        .logo-k { background: white; color: #2563eb; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-weight: 900; font-size: 16px; }

        @media (max-width: 480px) {
            body { padding: 8px; }
            .header { padding: 22px 16px; }
            .header h1 { font-size: 18px; }
            .body { padding: 18px 16px; }
            .footer { padding: 14px 16px; }
            .value { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <div class="header">
                <div class="logo-wrap">
                    <span class="logo-k">K</span>
                    <span style="color:white;font-size:16px;font-weight:bold;">Kliksy</span>
                </div>
                <h1>Nueva solicitud recibida</h1>
                <p>Alguien quiere contratar tus servicios</p>
            </div>

            <div class="body">

                <div class="row">
                    <p class="label">Cliente</p>
                    <p class="value">{{ $solicitud->cliente->name }}</p>
                </div>
                <hr class="divider">

                <div class="row">
                    <p class="label">Servicio solicitado</p>
                    <p class="value">{{ $solicitud->servicio->titulo }}</p>
                </div>
                <hr class="divider">

                <div class="row">
                    <p class="label">Precio</p>
                    <p class="value">${{ number_format($solicitud->servicio->precio, 0, ',', '.') }}</p>
                </div>
                <hr class="divider">

                <div class="row">
                    <p class="label">Fecha</p>
                    <p class="value">{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <hr class="divider">

                <div class="row">
                    <p class="label">Estado</p>
                    <span class="badge">Pendiente</span>
                </div>

                <a href="{{ url('/profesional/info') }}" class="cta">
                    Ver mis solicitudes →
                </a>

            </div>

            <div class="footer">
                Este correo fue enviado automáticamente por Kliksy.<br>
                Ingresa a la plataforma para aceptar o rechazar la solicitud.
            </div>

        </div>
    </div>
</body>
</html>