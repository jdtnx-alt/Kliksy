@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-400 text-sm mt-1">Resumen general de la plataforma</p>
</div>

{{-- FILA 1: Usuarios y Servicios --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div style="width:28px;height:4px;border-radius:2px;background:#2563eb;margin-bottom:12px;"></div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalUsuarios }}</p>
        <p class="text-sm text-gray-400 mt-1">Usuarios totales</p>
        <p class="text-xs text-gray-300 mt-1">{{ $totalClientes }} clientes · {{ $totalProfesionales }} profesionales</p>
        <div style="display:flex;align-items:flex-end;gap:4px;height:40px;margin-top:12px;">
            @foreach($usuariosPorMes as $mes)
            @php $maxMes = $usuariosPorMes->max('total'); $h = $maxMes > 0 ? round(($mes->total / $maxMes) * 100) : 10; @endphp
            <div style="flex:1;height:{{ $h }}%;background:#bfdbfe;border-radius:2px 2px 0 0;" title="{{ $mes->total }} usuarios"></div>
            @endforeach
            @if($usuariosPorMes->isEmpty())
            <div style="flex:1;height:30%;background:#bfdbfe;border-radius:2px 2px 0 0;"></div>
            <div style="flex:1;height:50%;background:#93c5fd;border-radius:2px 2px 0 0;"></div>
            <div style="flex:1;height:80%;background:#60a5fa;border-radius:2px 2px 0 0;"></div>
            <div style="flex:1;height:100%;background:#2563eb;border-radius:2px 2px 0 0;"></div>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div style="width:28px;height:4px;border-radius:2px;background:#16a34a;margin-bottom:12px;"></div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalServicios }}</p>
        <p class="text-sm text-gray-400 mt-1">Servicios publicados</p>
        <div style="display:flex;align-items:flex-end;gap:4px;height:40px;margin-top:24px;">
            @foreach($serviciosPorCategoria as $cat)
            @php $maxCat = $serviciosPorCategoria->max('total'); $h = $maxCat > 0 ? round(($cat->total / $maxCat) * 100) : 10; @endphp
            <div style="flex:1;height:{{ $h }}%;background:#bbf7d0;border-radius:2px 2px 0 0;" title="{{ $cat->categoria }}: {{ $cat->total }}"></div>
            @endforeach
        </div>
    </div>

</div>

{{-- FILA 2: Reseñas, Reservas, Reportes --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div style="width:28px;height:4px;border-radius:2px;background:#7c3aed;margin-bottom:12px;"></div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalResenas }}</p>
        <p class="text-xs sm:text-sm text-gray-400 mt-1">Reseñas</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div style="width:28px;height:4px;border-radius:2px;background:#d97706;margin-bottom:12px;"></div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalReservas }}</p>
        <p class="text-xs sm:text-sm text-gray-400 mt-1">Reservas</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <div style="width:28px;height:4px;border-radius:2px;background:#dc2626;margin-bottom:12px;"></div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalReportes }}</p>
        <p class="text-xs sm:text-sm text-gray-400 mt-1">Reportes</p>
    </div>

</div>

{{-- FILA NUEVA: Métricas de negocio --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <p class="text-xs text-gray-400 mb-1">Nuevos usuarios (Semana)</p>
        <p class="text-xl font-bold text-blue-600">+{{ $nuevosEstaSemana }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <p class="text-xs text-gray-400 mb-1">Profesionales Activos</p>
        <p class="text-xl font-bold text-green-600">{{ $profesionalesActivos }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <p class="text-xs text-gray-400 mb-1">Clientes Activos</p>
        <p class="text-xl font-bold text-purple-600">{{ $clientesActivos }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <p class="text-xs text-gray-400 mb-1">Tasa Cancelación</p>
        <p class="text-xl font-bold text-red-500">{{ $tasaCancelacion }}%</p>
    </div>
</div>

{{-- FILA NUEVA: Financiero --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 relative overflow-hidden">
        <i class="bi bi-wallet2 absolute -right-4 -bottom-4 text-7xl text-blue-100 opacity-50"></i>
        <p class="text-sm font-semibold text-blue-800 mb-1">Dinero Retenido</p>
        <p class="text-3xl font-bold text-blue-600">${{ number_format($dineroRetenido, 0, ',', '.') }}</p>
        <p class="text-xs text-blue-500 mt-1">En escrow (garantía)</p>
    </div>
    <div class="bg-green-50 border border-green-100 rounded-2xl p-5 relative overflow-hidden">
        <i class="bi bi-cash-stack absolute -right-4 -bottom-4 text-7xl text-green-100 opacity-50"></i>
        <p class="text-sm font-semibold text-green-800 mb-1">Dinero Liberado</p>
        <p class="text-3xl font-bold text-green-600">${{ number_format($dineroLiberado, 0, ',', '.') }}</p>
        <p class="text-xs text-green-500 mt-1">Pagos a profesionales</p>
    </div>
    <div class="bg-red-50 border border-red-100 rounded-2xl p-5 relative overflow-hidden">
        <i class="bi bi-arrow-counterclockwise absolute -right-4 -bottom-4 text-7xl text-red-100 opacity-50"></i>
        <p class="text-sm font-semibold text-red-800 mb-1">Dinero Reembolsado</p>
        <p class="text-3xl font-bold text-red-500">${{ number_format($dineroReembolsado, 0, ',', '.') }}</p>
        <p class="text-xs text-red-400 mt-1">Servicios cancelados</p>
    </div>
</div>

{{-- FILA 3: Top categorías y Solicitudes por estado --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">

    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:col-span-2">
        <p class="text-sm font-semibold text-gray-700 mb-4">Top categorías</p>
        @php $maxCat = $serviciosPorCategoria->max('total') ?: 1; @endphp
        @forelse($serviciosPorCategoria as $cat)
        <div class="flex items-center gap-2 sm:gap-3 mb-2">
            <div class="text-xs text-gray-500 flex-shrink-0" style="width:80px;">{{ ucfirst($cat->categoria) }}</div>
            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width:{{ round(($cat->total/$maxCat)*100) }}%"></div>
            </div>
            <div class="text-xs text-gray-500 w-4 text-right flex-shrink-0">{{ $cat->total }}</div>
        </div>
        @empty
        <p class="text-sm text-gray-400">Sin datos</p>
        @endforelse
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <p class="text-sm font-semibold text-gray-700 mb-4">Estado de Reservas</p>
        <div class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Completadas</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-green-100 text-green-700">{{ $reservasCompletadas }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Pendientes</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">{{ $reservasPendientes }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Confirmadas</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $reservasConfirmadas }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Canceladas</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-red-100 text-red-700">{{ $reservasCanceladas }}</span>
            </div>
        </div>
    </div>

</div>

{{-- FILA 4: Top profesionales --}}
<div class="bg-white border border-gray-200 rounded-2xl p-5">
    <p class="text-sm font-semibold text-gray-700 mb-4">Top profesionales por reseñas</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        @forelse($topProfesionales as $prof)
        <div class="flex flex-col items-center text-center p-3 bg-gray-50 rounded-xl">
            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-sm mb-2">
                {{ strtoupper(substr($prof->name, 0, 2)) }}
            </div>
            <p class="text-xs font-medium text-gray-800 mb-0.5">{{ explode(' ', $prof->name)[0] }}</p>
            <p class="text-xs text-gray-400">{{ $prof->resenas_count }} reseñas</p>
        </div>
        @empty
        <p class="text-sm text-gray-400 col-span-5">Sin profesionales aún.</p>
        @endforelse
    </div>
</div>

@endsection