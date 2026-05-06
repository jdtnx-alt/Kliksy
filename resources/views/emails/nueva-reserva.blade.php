<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header .logo { background: white; color: #2563eb; width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px; margin-bottom: 16px; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.7); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 20px; }
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
        <h1>¡Nueva reserva recibida!</h1>
        <p>Un cliente ha reservado uno de tus servicios</p>
    </div>
    <div class="body">
        <p class="greeting">Hola <strong>{{ $profesional->name }}</strong>,</p>
        <p style="color:#6b7280;font-size:14px;margin-bottom:20px;">
            <strong>{{ $cliente->name }}</strong> ha realizado una reserva y el pago está retenido en Kliksy esperando tu confirmación.
        </p>

        <div class="card">
            <div class="card-row">
                <span class="label">Servicio</span>
                <span class="value">{{ $servicio->titulo }}</span>
            </div>
            <div class="card-row">
                <span class="label">Cliente</span>
                <span class="value">{{ $cliente->name }}</span>
            </div>
            <div class="card-row">
                <span class="label">Fecha</span>
                <span class="value">{{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}</span>
            </div>
            <div class="card-row">
                <span class="label">Hora</span>
                <span class="value">{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i A') }} – {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('g:i A') }}</span>
            </div>
            <div class="card-row">
                <span class="label">Monto retenido</span>
                <span class="value" style="color:#2563eb">${{ number_format($reserva->monto, 0, ',', '.') }} COP</span>
            </div>
            @if($reserva->nota_cliente)
            <div class="card-row">
                <span class="label">Nota del cliente</span>
                <span class="value">{{ $reserva->nota_cliente }}</span>
            </div>
            @endif
        </div>

        <a href="{{ url('/profesional/reservas') }}" class="btn">
            Ver reserva en mi dashboard →
        </a>

        <p style="color:#9ca3af;font-size:12px;text-align:center;">
            Si no puedes atender esta reserva, recuerda cancelarla para que el cliente reciba su reembolso automáticamente.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Kliksy — Florencia, Caquetá
    </div>
</div>
</body>
</html>