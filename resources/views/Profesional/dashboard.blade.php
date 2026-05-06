@extends('layouts.profesional')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
    use App\Helpers\OnboardingHelper;
    use App\Models\Reserva;
    use Carbon\Carbon;

    $perfil         = auth()->user()->perfilProfesional;
    $activo         = OnboardingHelper::perfilActivo(auth()->user());
    $progreso       = OnboardingHelper::progreso(auth()->user());
    $enVacaciones   = $perfil?->en_vacaciones ?? false;

    // FECHAS
    $mesPasado      = now()->subMonth();
    $anioActual     = now()->year;
    $mesActual      = now()->month;

    // ====== NUEVAS ESTADÍSTICAS Y CORRECCIONES ======
    
    // INGRESOS
    $estadosIngreso = ['liberado']; 
    
    $ingresosTotales    = Reserva::where('profesional_id', auth()->id())->whereIn('estado_pago', $estadosIngreso)->sum('monto');
    $ingresosRetenidos  = Reserva::where('profesional_id', auth()->id())->where('estado_pago', 'retenido')->sum('monto');
    
    $ingresosMes        = Reserva::where('profesional_id', auth()->id())->whereIn('estado_pago', $estadosIngreso)->whereMonth('fecha', $mesActual)->whereYear('fecha', $anioActual)->sum('monto');
    $ingresosMesPasado  = Reserva::where('profesional_id', auth()->id())->whereIn('estado_pago', $estadosIngreso)->whereMonth('fecha', $mesPasado->month)->whereYear('fecha', $mesPasado->year)->sum('monto');
    
    $crecimientoIngresos = $ingresosMesPasado > 0 ? (($ingresosMes - $ingresosMesPasado) / $ingresosMesPasado) * 100 : ($ingresosMes > 0 ? 100 : 0);

    // CITAS Y TICKET
    $ticketPromedio     = Reserva::where('profesional_id', auth()->id())->where('estado', 'completada')->avg('monto') ?? 0;
    
    $citasMesActual     = Reserva::where('profesional_id', auth()->id())->whereMonth('fecha', $mesActual)->whereYear('fecha', $anioActual)->where('estado', 'completada')->count();
    $citasMesPasado     = Reserva::where('profesional_id', auth()->id())->whereMonth('fecha', $mesPasado->month)->whereYear('fecha', $mesPasado->year)->where('estado', 'completada')->count();
    $crecimientoCitas   = $citasMesPasado > 0 ? (($citasMesActual - $citasMesPasado) / $citasMesPasado) * 100 : ($citasMesActual > 0 ? 100 : 0);

    // ESTADÍSTICAS GENERALES DE ÉXITO
    $totalCompletadas = Reserva::where('profesional_id', auth()->id())->where('estado', 'completada')->count();
    $totalCanceladas  = Reserva::where('profesional_id', auth()->id())->where('estado', 'cancelada')->count();
    $totalReservas    = Reserva::where('profesional_id', auth()->id())->whereNotIn('estado', ['pendiente_pago'])->count();
    $tasaExito        = $totalReservas > 0 ? round(($totalCompletadas / $totalReservas) * 100) : 0;

    // SERVICIO MÁS POPULAR
    $servicioMasPopular = Reserva::with('servicio')
        ->where('profesional_id', auth()->id())
        ->where('estado', 'completada')
        ->get()
        ->groupBy('servicio_id')
        ->map(fn($reservas) => [
            'total' => $reservas->count(), 
            'ingresos' => $reservas->sum('monto'),
            'servicio' => $reservas->first()->servicio
        ])
        ->sortByDesc('total')
        ->first();

    // RESERVAS PRÓXIMAS
    $proximasReservas   = Reserva::with(['cliente', 'servicio'])->where('profesional_id', auth()->id())->whereIn('estado', ['pendiente', 'confirmada'])->where('fecha', '>=', now()->toDateString())->orderBy('fecha')->orderBy('hora_inicio')->take(5)->get();

    // CALENDARIO — mes actual
    $diasConReserva = Reserva::where('profesional_id', auth()->id())->whereMonth('fecha', $mesActual)->whereYear('fecha', $anioActual)->whereNotIn('estado', ['cancelada'])->get()->groupBy(fn($r) => $r->fecha->day);

    // GRÁFICA OPTIMIZADA CON UNA SOLA CONSULTA
    $anioGrafica  = request('anio', now()->year);
    $reservasAnio = Reserva::where('profesional_id', auth()->id())
        ->whereIn('estado_pago', $estadosIngreso)
        ->whereYear('fecha', $anioGrafica)
        ->get();

    $ingresosPorMes = array_fill(1, 12, 0);
    $citasPorMes = array_fill(1, 12, 0);

    foreach ($reservasAnio as $reserva) {
        $mes = Carbon::parse($reserva->fecha)->month;
        $ingresosPorMes[$mes] += $reserva->monto;
        $citasPorMes[$mes]++;
    }
    
    $maxIngreso = max($ingresosPorMes) ?: 1;
    $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // Días del mes para el calendario
    $primerDia     = Carbon::create($anioActual, $mesActual, 1);
    $diasEnMes     = $primerDia->daysInMonth;
    $iniciaSemana  = $primerDia->dayOfWeekIso; // 1=Lun, 7=Dom
@endphp

{{-- BANNER ONBOARDING --}}
@if(!$activo)
<div class="bg-yellow-50 border border-yellow-300 rounded-2xl px-4 py-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="bi bi-eye-slash text-yellow-600 text-lg"></i>
        </div>
        <div>
            <p class="font-semibold text-yellow-800 text-sm">Tu perfil aún no es visible para los clientes</p>
            <p class="text-yellow-600 text-xs mt-0.5">Completa los pasos requeridos para aparecer en búsquedas</p>
        </div>
    </div>
    <a href="{{ route('profesional.onboarding') }}"
        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap flex-shrink-0 text-center transition shadow-sm">
        Ver progreso ({{ $progreso }}%)
    </a>
</div>
@endif

{{-- MAIN STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <!-- Ingresos Mes -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 hover:shadow-md transition duration-300 group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg lg:text-xl group-hover:scale-110 transition-transform">
                <i class="bi bi-currency-dollar"></i>
            </div>
            @if($crecimientoIngresos != 0)
                <span class="text-xs font-semibold px-2 py-1 rounded-full flex items-center gap-1 {{ $crecimientoIngresos > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    <i class="bi bi-caret-{{ $crecimientoIngresos > 0 ? 'up' : 'down' }}-fill"></i>
                    {{ abs(round($crecimientoIngresos, 1)) }}%
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500 font-medium">Ingresos este mes</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($ingresosMes, 0, ',', '.') }}</p>
    </div>

    <!-- Citas Mes -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-green-300 hover:shadow-md transition duration-300 group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg lg:text-xl group-hover:scale-110 transition-transform">
                <i class="bi bi-calendar-check"></i>
            </div>
            @if($crecimientoCitas != 0)
                <span class="text-xs font-semibold px-2 py-1 rounded-full flex items-center gap-1 {{ $crecimientoCitas > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    <i class="bi bi-caret-{{ $crecimientoCitas > 0 ? 'up' : 'down' }}-fill"></i>
                    {{ abs(round($crecimientoCitas, 1)) }}%
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500 font-medium">Citas completadas</p>
        <div class="flex items-end gap-2 mt-1">
            <p class="text-2xl font-bold text-gray-800">{{ $citasMesActual }}</p>
            <p class="text-xs text-gray-400 mb-1 lg:inline hidden">en este mes</p>
        </div>
    </div>
    
    <!-- Tasa Éxito -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-purple-300 hover:shadow-md transition duration-300 group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg lg:text-xl group-hover:scale-110 transition-transform">
                <i class="bi bi-check2-circle"></i>
            </div>
            <span class="text-[10px] uppercase font-bold text-gray-400">Total</span>
        </div>
        <p class="text-xs text-gray-500 font-medium">Tasa de éxito</p>
        <div class="flex items-center gap-3 mt-1">
            <p class="text-2xl font-bold text-gray-800">{{ $tasaExito }}%</p>
            <div class="flex-1 bg-gray-100 rounded-full h-1.5 hidden sm:block">
                <div class="bg-purple-500 h-1.5 rounded-full" style="width:{{ $tasaExito }}%"></div>
            </div>
        </div>
    </div>

    <!-- Retenidos -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-amber-300 hover:shadow-md transition duration-300 group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg lg:text-xl group-hover:scale-110 transition-transform">
                <i class="bi bi-lock"></i>
            </div>
            <a href="{{ route('profesional.historial.exportar') }}" class="text-[10px] uppercase font-bold text-amber-600 hover:text-amber-700 bg-amber-100/50 px-2 py-1 rounded">Ver más</a>
        </div>
        <p class="text-xs text-gray-500 font-medium">Retenidos (En proceso)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($ingresosRetenidos, 0, ',', '.') }}</p>
    </div>
</div>

{{-- SECONDARY STATS ROW --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-center gap-4 hover:bg-white hover:shadow-sm transition">
        <div class="w-10 h-10 bg-white shadow-sm border border-gray-100 rounded-full flex items-center justify-center text-gray-500 flex-shrink-0">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-500 uppercase font-semibold">Ticket promedio</p>
            <p class="text-base font-bold text-gray-800">${{ number_format($ticketPromedio, 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-center gap-4 hover:bg-white hover:shadow-sm transition">
        <div class="w-10 h-10 bg-white shadow-sm border border-gray-100 rounded-full flex items-center justify-center text-gray-500 flex-shrink-0">
            <i class="bi bi-wallet2"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-500 uppercase font-semibold">Ingresos históricos</p>
            <p class="text-base font-bold text-gray-800">${{ number_format($ingresosTotales, 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-center gap-4 hover:bg-white hover:shadow-sm transition">
        <div class="w-10 h-10 bg-indigo-50 shadow-sm border border-indigo-100 rounded-full flex items-center justify-center text-indigo-500 flex-shrink-0">
            <i class="bi bi-star"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[11px] text-gray-500 uppercase font-semibold">Servicio Top</p>
            @if($servicioMasPopular)
                <p class="text-base font-bold text-gray-800 truncate" title="{{ $servicioMasPopular['servicio']->titulo }}">{{ $servicioMasPopular['servicio']->titulo }} <span class="text-xs font-normal text-gray-500 ml-1">({{ $servicioMasPopular['total'] }})</span></p>
            @else
                <p class="text-base font-bold text-gray-800">-</p>
            @endif
        </div>
    </div>
</div>

{{-- GRÁFICA + CALENDARIO --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- GRÁFICA --}}
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm">
                <i class="bi bi-bar-chart text-blue-500"></i> Ingresos del año
            </h3>
            <div class="flex gap-2">
                @foreach([now()->year - 1, now()->year] as $anio)
                <a href="{{ request()->fullUrlWithQuery(['anio' => $anio]) }}"
                    class="text-[11px] font-medium px-3 py-1.5 rounded-lg border transition
                    {{ $anioGrafica == $anio ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50 hover:border-gray-300' }}">
                    {{ $anio }}
                </a>
                @endforeach
            </div>
        </div>
        <div class="flex items-end gap-2 h-56 px-2 mt-8">
            @foreach($ingresosPorMes as $mes => $ingreso)
            @php
                $altura = $maxIngreso > 0 ? max(4, round(($ingreso / $maxIngreso) * 100)) : 4;
                $esActual = $mes == now()->month && $anioGrafica == now()->year;
                $cantidad = $citasPorMes[$mes];
            @endphp
            <div class="flex flex-col items-center justify-end flex-1 gap-1.5 group relative h-full">
                <!-- Tooltip visible on hover -->
                @if($ingreso > 0)
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2.5 py-1.5 rounded-xl opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap z-10 shadow-xl pointer-events-none flex flex-col items-center">
                    <span class="font-bold">${{ number_format($ingreso, 0, ',', '.') }}</span>
                    <span class="text-gray-300 text-[10px] font-medium">{{ $cantidad }} cita{{ $cantidad != 1 ? 's' : '' }}</span>
                    <div class="w-2.5 h-2.5 bg-gray-900 rotate-45 absolute -bottom-1 rounded-sm"></div>
                </div>
                @else
                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] uppercase font-bold px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap z-10 shadow-xl pointer-events-none">
                    Sin ingresos
                    <div class="w-2 h-2 bg-gray-900 rotate-45 absolute -bottom-0.5 left-1/2 -translate-x-1/2 rounded-sm"></div>
                </div>
                @endif
                
                <!-- Barra del gráfico -->
                <div class="w-full rounded-t-lg transition-all duration-300 hover:bg-blue-500 cursor-pointer"
                    style="height: {{ $altura }}%; background: {{ $esActual ? '#3b82f6' : ($ingreso > 0 ? '#dbeafe' : '#f3f4f6') }};">
                </div>
                <span class="text-[10px] lg:text-xs text-gray-400 font-medium uppercase mt-1">{{ $meses[$mes - 1] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CALENDARIO --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2 mb-4">
            <i class="bi bi-calendar3 text-blue-500"></i>
            {{ Carbon::create($anioActual, $mesActual)->locale('es')->monthName }} {{ $anioActual }}
        </h3>

        {{-- Cabecera días --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
            @foreach(['L','M','M','J','V','S','D'] as $d)
            <div class="text-center text-xs font-semibold text-gray-400">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Días --}}
        <div class="grid grid-cols-7 gap-1">
            {{-- Espacios vacíos --}}
            @for($i = 1; $i < $iniciaSemana; $i++)
            <div></div>
            @endfor

            @for($dia = 1; $dia <= $diasEnMes; $dia++)
            @php
                $reservasDelDia = $diasConReserva[$dia] ?? collect();
                $cantReservas   = $reservasDelDia->count();
                $esHoy          = $dia == now()->day && $mesActual == now()->month && $anioActual == now()->year;
            @endphp
            <div class="aspect-square rounded-lg flex flex-col items-center justify-center text-xs cursor-default relative
                {{ $esHoy ? 'bg-blue-600 text-white font-bold' : ($cantReservas >= 3 ? 'bg-red-100 text-red-600 font-semibold' : ($cantReservas > 0 ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-500 hover:bg-gray-50')) }}">
                {{ $dia }}
                @if($cantReservas > 0 && !$esHoy)
                <div class="w-1 h-1 rounded-full mt-0.5 {{ $cantReservas >= 3 ? 'bg-red-400' : 'bg-blue-400' }}"></div>
                @endif
            </div>
            @endfor
        </div>

        {{-- Leyenda --}}
        <div class="flex gap-3 mt-4 flex-wrap">
            <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-sm bg-blue-50 border border-blue-200"></div><span class="text-xs text-gray-400">Con reserva</span></div>
            <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-sm bg-red-100"></div><span class="text-xs text-gray-400">Día lleno</span></div>
            <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-sm bg-blue-600"></div><span class="text-xs text-gray-400">Hoy</span></div>
        </div>
    </div>
</div>

{{-- PRÓXIMAS RESERVAS --}}
<div class="bg-white border border-gray-200 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
            <i class="bi bi-clock text-blue-500"></i> Próximas reservas
        </h3>
        <div class="flex items-center gap-3">
            <a href="{{ route('profesional.historial.exportar') }}"
                class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 px-3 py-1.5 rounded-xl transition">
                <i class="bi bi-download text-xs"></i> Exportar historial
            </a>
            <a href="{{ route('profesional.reservas') }}" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
        </div>
    </div>

    @forelse($proximasReservas as $reserva)
    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
            <span class="text-sm font-bold text-blue-600 leading-none">{{ $reserva->fecha->format('d') }}</span>
            <span class="text-xs text-blue-400 uppercase">{{ $reserva->fecha->locale('es')->shortMonthName }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">{{ $reserva->cliente->name }}</p>
            <p class="text-xs text-gray-400">
                {{ Carbon::parse($reserva->hora_inicio)->format('g:i A') }} –
                {{ Carbon::parse($reserva->hora_fin)->format('g:i A') }} ·
                {{ $reserva->servicio->titulo }}
            </p>
        </div>
        <span class="text-sm font-bold text-green-600 flex-shrink-0">${{ number_format($reserva->monto, 0, ',', '.') }}</span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0
            {{ $reserva->estado === 'confirmada' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ucfirst($reserva->estado) }}
        </span>
    </div>
    @empty
    <div class="text-center py-8 text-gray-400">
        <i class="bi bi-calendar-x text-4xl mb-3 block"></i>
        <p class="text-sm font-medium text-gray-500">No tienes reservas próximas</p>
    </div>
    @endforelse
</div>
{{-- CLIENTES RECURRENTES --}}
@php
    $clientesRecurrentes = \App\Models\Reserva::with('cliente')
        ->where('profesional_id', auth()->id())
        ->where('estado', 'completada')
        ->get()
        ->groupBy('cliente_id')
        ->filter(fn($reservas) => $reservas->count() > 1)
        ->map(fn($reservas) => [
            'cliente'   => $reservas->first()->cliente,
            'visitas'   => $reservas->count(),
            'total'     => $reservas->sum('monto'),
            'ultima'    => $reservas->sortByDesc('fecha')->first()->fecha,
        ])
        ->sortByDesc('visitas')
        ->take(5);
@endphp

@if($clientesRecurrentes->count())
<div class="bg-white border border-gray-200 rounded-2xl p-5 mt-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
            <i class="bi bi-arrow-repeat text-purple-500"></i> Clientes recurrentes
        </h3>
        <span class="text-xs bg-purple-50 text-purple-600 px-2.5 py-1 rounded-full font-medium">
            {{ $clientesRecurrentes->count() }} clientes fieles
        </span>
    </div>
    @foreach($clientesRecurrentes as $cr)
    <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
        <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-sm flex-shrink-0">
            {{ strtoupper(substr($cr['cliente']->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">{{ $cr['cliente']->name }}</p>
            <p class="text-xs text-gray-400">Última visita: {{ $cr['ultima']->diffForHumans() }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-sm font-bold text-purple-600">{{ $cr['visitas'] }} visitas</p>
            <p class="text-xs text-gray-400">${{ number_format($cr['total'], 0, ',', '.') }} total</p>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ANÁLISIS IA DE RESEÑAS --}}
@if(auth()->user()->resenas()->count() >= 2)
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 mt-5" id="iaCard">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
            <i class="bi bi-stars text-blue-500"></i> Análisis IA de tus reseñas
        </h3>
        <button onclick="generarAnalisis()"
            id="btnAnalisis"
            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-xl transition cursor-pointer font-medium flex items-center gap-1.5">
            <i class="bi bi-magic"></i> Generar análisis
        </button>
    </div>
    <div id="iaResultado" class="hidden">
        <p id="iaTexto" class="text-sm text-gray-700 leading-relaxed"></p>
    </div>
    <div id="iaCargando" class="hidden">
        <div class="flex items-center gap-2 text-sm text-blue-600">
            <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            Analizando tus reseñas...
        </div>
    </div>
    <p class="text-xs text-gray-400 mt-2">Claude analiza tus últimas reseñas y genera un resumen de tus fortalezas.</p>
</div>
@endif

@push('scripts')
<script>
function generarAnalisis() {
    const btn = document.getElementById('btnAnalisis');
    const cargando = document.getElementById('iaCargando');
    const resultado = document.getElementById('iaResultado');
    const texto = document.getElementById('iaTexto');

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Analizando...';
    cargando.classList.remove('hidden');
    resultado.classList.add('hidden');

    fetch('{{ route('profesional.analisis.resenas') }}')
        .then(r => r.json())
        .then(data => {
            cargando.classList.add('hidden');
            if (data.resumen) {
                texto.textContent = data.resumen;
                resultado.classList.remove('hidden');
                btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Regenerar';
            } else {
                texto.textContent = data.mensaje;
                resultado.classList.remove('hidden');
                btn.innerHTML = '<i class="bi bi-magic"></i> Generar análisis';
            }
            btn.disabled = false;
        })
        .catch(() => {
            cargando.classList.add('hidden');
            texto.textContent = 'Error al conectar con el servicio de IA.';
            resultado.classList.remove('hidden');
            btn.innerHTML = '<i class="bi bi-magic"></i> Generar análisis';
            btn.disabled = false;
        });
}
</script>
@endpush

@endsection