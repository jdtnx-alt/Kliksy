<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #16a34a; padding: 32px; text-align: center; }
        .header .logo { background: white; color: #16a34a; width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px; margin-bottom: 16px; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.8); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .card-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; }
        .card-row:last-child { border-bottom: none; }
        .card-row .label { color: #6b7280; }
        .card-row .value { color: #111827; font-weight: 600; }
        .btn { display: block; background: #2563eb; color: white; text-decoration: none; padding: 14px 24px; border-radius: 12px; text-align: center; font-weight: 700; font-size: 15px; margin: 24px 0; }
        .footer { padding: 20px 32px; border-top: 1px solid #f0f0f0; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">K</div>
        <h1>¡Servicio completado!</h1>
        <p>Tu servicio ha sido marcado como completado</p>
    </div>
    <div class="body">
        <p style="color:#374151;font-size:16px;margin-bottom:20px;">Hola <strong>{{ $cliente->name }}</strong>,</p>
        <p style="color:#6b7280;font-size:14px;margin-bottom:20px;">
            <strong>{{ $profesional->name }}</strong> ha marcado tu servicio como completado. El pago ha sido procesado correctamente.
        </p>

        <div class="card">
            <div class="card-row">
                <span class="label">Servicio</span>
                <span class="value">{{ $servicio->titulo }}</span>
            </div>
            <div class="card-row">
                <span class="label">Profesional</span>
                <span class="value">{{ $profesional->name }}</span>
            </div>
            <div class="card-row">
                <span class="label">Fecha</span>
                <span class="value">{{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}</span>
            </div>
            <div class="card-row">
                <span class="label">Total pagado</span>
                <span class="value" style="color:#16a34a">${{ number_format($reserva->monto, 0, ',', '.') }} COP</span>
            </div>
        </div>

        <a href="{{ url('/mis-reservas') }}" class="btn">
            ⭐ Dejar reseña al profesional
        </a>

        <p style="color:#9ca3af;font-size:12px;text-align:center;">
            Tu opinión ayuda a otros clientes a elegir al profesional correcto.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Kliksy — Florencia, Caquetá
    </div>
</div>
</body>
</html>