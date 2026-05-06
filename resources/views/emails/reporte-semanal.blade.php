<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; font-weight: 800; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin: 6px 0 0; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .sub { font-size: 13px; color: #6b7280; margin-bottom: 24px; }
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .stat { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; text-align: center; }
        .stat-val { font-size: 24px; font-weight: 800; color: #111827; }
        .stat-val.green { color: #16a34a; }
        .stat-val.blue { color: #2563eb; }
        .stat-val.yellow { color: #d97706; }
        .stat-label { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .section-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6; }
        .reserva-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb; }
        .reserva-row:last-child { border: none; }
        .rv-name { font-size: 13px; font-weight: 600; color: #111827; }
        .rv-svc { font-size: 11px; color: #6b7280; }
        .rv-monto { font-size: 13px; font-weight: 700; color: #16a34a; }
        .tip { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; margin-top: 24px; }
        .tip p { font-size: 12px; color: #1d4ed8; margin: 0; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 11px; color: #9ca3af; margin: 0; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 12px 28px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📊 Tu reporte semanal</h1>
        <p>{{ now()->startOfWeek()->format('d M') }} — {{ now()->endOfWeek()->format('d M, Y') }}</p>
    </div>
    <div class="body">
        <p class="greeting">¡Hola, {{ $nombreProfesional }}!</p>
        <p class="sub">Aquí tienes el resumen de tu actividad esta semana en Kliksy.</p>

        <div class="stats">
            <div class="stat">
                <div class="stat-val green">${{ number_format($ingresosSemana, 0, ',', '.') }}</div>
                <div class="stat-label">Ingresos esta semana</div>
            </div>
            <div class="stat">
                <div class="stat-val blue">{{ $reservasSemana }}</div>
                <div class="stat-label">Reservas completadas</div>
            </div>
            <div class="stat">
                <div class="stat-val yellow">{{ number_format($promedioCalificacion, 1) }} ★</div>
                <div class="stat-label">Calificación promedio</div>
            </div>
            <div class="stat">
                <div class="stat-val">{{ $nuevasResenas }}</div>
                <div class="stat-label">Nuevas reseñas</div>
            </div>
        </div>

        @if($reservasDetalle->count())
        <p class="section-title">Reservas de esta semana</p>
        @foreach($reservasDetalle as $r)
        <div class="reserva-row">
            <div>
                <div class="rv-name">{{ $r->cliente->name }}</div>
                <div class="rv-svc">{{ $r->servicio->titulo }} · {{ $r->fecha->format('d M') }}</div>
            </div>
            <div class="rv-monto">${{ number_format($r->monto, 0, ',', '.') }}</div>
        </div>
        @endforeach
        @endif

        @if($ingresosSemana > $ingresosSemanaAnterior)
        <div class="tip">
            <p>📈 ¡Tus ingresos subieron un {{ $variacion }}% respecto a la semana anterior! Sigue así.</p>
        </div>
        @elseif($ingresosSemana < $ingresosSemanaAnterior)
        <div class="tip" style="background:#fff7ed;border-color:#fed7aa;">
            <p style="color:#c2410c;">💡 Esta semana tuviste menos ingresos que la anterior. Considera actualizar tu perfil o agregar nuevos servicios.</p>
        </div>
        @endif

        <div style="text-align:center">
            <a href="{{ url('/profesional/dashboard') }}" class="btn">Ver mi dashboard →</a>
        </div>
    </div>
    <div class="footer">
        <p>Kliksy · Servicios a domicilio en Florencia, Caquetá</p>
        <p style="margin-top:4px">Recibes este correo porque eres profesional en Kliksy.</p>
    </div>
</div>
</body>
</html>