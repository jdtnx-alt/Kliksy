@extends('layouts.profesional')
@section('title', 'Mis Reservas')
@section('page_title', 'Mis Reservas')

@section('content')

{{-- ADVERTENCIA EVIDENCIAS --}}
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-2xl p-4 mb-6 shadow-sm">
    <div class="flex gap-3">
        <i class="bi bi-shield-check text-blue-600 text-xl font-bold mt-0.5"></i>
        <div>
            <h3 class="font-bold text-blue-800 text-sm">Toma evidencias de tu trabajo para evitar estafas</h3>
            <p class="text-blue-600 text-xs mt-1">Recuerda siempre tomar <b>fotos o capturas</b> de tus servicios finalizados. Cuando marques un servicio como completado, el cliente deberá confirmarlo. Si el cliente reporta que el servicio no fue realizado, el administrador te pedirá pruebas para resolver la disputa y garantizar tu pago.</p>
        </div>
    </div>
</div>

{{-- FILTROS --}}
<div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('profesional.reservas') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Filtrar por fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}"
                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
            <select name="estado"
                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Todos los estados</option>
                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmada" {{ request('estado') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                <option value="cancelada"  {{ request('estado') === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl text-sm font-semibold transition cursor-pointer">
                <i class="bi bi-search"></i> Filtrar
            </button>
            @if(request('fecha') || request('estado'))
            <a href="{{ route('profesional.reservas') }}"
                class="border border-gray-200 hover:bg-gray-50 text-gray-500 px-4 py-3 rounded-xl text-sm transition">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </div>
    </form>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 text-sm flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- RESERVAS AGRUPADAS POR FECHA --}}
@forelse($reservasPorFecha as $fecha => $reservasDelDia)
<div class="mb-6">

    {{-- CABECERA DE FECHA --}}
    <div class="flex items-center gap-3 mb-3">
        <div class="bg-blue-600 text-white rounded-xl px-4 py-2 text-sm font-bold flex-shrink-0">
            {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('ddd D MMM') }}
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400 flex-shrink-0">{{ $reservasDelDia->count() }} reserva{{ $reservasDelDia->count() > 1 ? 's' : '' }}</span>
    </div>

    {{-- RESERVAS DEL DÍA --}}
    <div class="flex flex-col gap-3">
        @foreach($reservasDelDia as $reserva)
        @php
            $badgeClass = match($reserva->estado) {
                'pendiente'  => 'bg-yellow-100 text-yellow-700',
                'confirmada' => 'bg-blue-100 text-blue-700',
                'completada' => 'bg-green-100 text-green-700',
                'cancelada'  => 'bg-red-100 text-red-500',
                default      => 'bg-gray-100 text-gray-500',
            };
            $pagoClass = match($reserva->estado_pago) {
                'retenido'    => 'bg-orange-100 text-orange-700',
                'liberado'    => 'bg-green-100 text-green-700',
                'reembolsado' => 'bg-gray-100 text-gray-500',
                default       => 'bg-gray-100 text-gray-400',
            };
        @endphp
        <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-200 transition">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                {{-- HORA --}}
                <div class="flex-shrink-0 text-center bg-gray-50 rounded-xl px-4 py-3 min-w-[80px]">
                    <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i') }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('A') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $reserva->servicio->duracion }} min</p>
                </div>

                {{-- INFO --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <p class="font-bold text-gray-800 text-sm">{{ $reserva->cliente->name }}</p>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium {{ $badgeClass }}">
                            {{ ucfirst($reserva->estado) }}
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium {{ $pagoClass }}">
                            Pago: {{ ucfirst($reserva->estado_pago) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1 flex items-center gap-1.5">
                        <i class="bi bi-scissors text-blue-400 text-xs"></i>
                        {{ $reserva->servicio->titulo }}
                    </p>
                    @if($reserva->nota_cliente)
                    <p class="text-xs text-gray-400 flex items-center gap-1.5 mt-1">
                        <i class="bi bi-chat-text text-gray-300"></i>
                        {{ $reserva->nota_cliente }}
                    </p>
                    @endif
                    <p class="text-sm font-bold text-blue-600 mt-2">
                        ${{ number_format($reserva->monto, 0, ',', '.') }} COP
                    </p>
                </div>

                {{-- ACCIONES --}}
                <div class="flex flex-col gap-2 flex-shrink-0">
                    @if($reserva->estado === 'pendiente')
                    <form method="POST" action="{{ route('reservas.aceptar', $reserva->id) }}">
                        @csrf
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
                            <i class="bi bi-check-lg"></i> Aceptar
                        </button>
                    </form>
                    <form method="POST" action="{{ route('reservas.cancelar', $reserva->id) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('¿Cancelar esta reserva? El cliente recibirá su reembolso.')"
                            class="w-full border border-gray-200 hover:border-red-400 hover:text-red-500 text-gray-500 px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
                            <i class="bi bi-x"></i> Rechazar
                        </button>
                    </form>
                    @elseif($reserva->estado === 'confirmada')
@if($reserva->cliente->telefono)
<div class="text-xs text-gray-500 flex items-center gap-1.5 mb-2 bg-gray-50 px-3 py-2 rounded-xl">
    <i class="bi bi-telephone text-blue-400"></i>
    <span>{{ $reserva->cliente->telefono }}</span>
</div>
@endif
<form method="POST" action="{{ route('reservas.completar', $reserva->id) }}">
    @csrf
    <button type="submit"
        class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
        <i class="bi bi-check-circle"></i> Completar
    </button>
</form>
                    <form method="POST" action="{{ route('reservas.cancelar', $reserva->id) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('¿Cancelar esta reserva?')"
                            class="w-full border border-gray-200 hover:border-red-400 hover:text-red-500 text-gray-500 px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer">
                            <i class="bi bi-x"></i> Cancelar
                        </button>
                    </form>
                    @elseif($reserva->estado === 'completada')
                        @if($reserva->confirmacion_cliente === 'pendiente')
                        <span class="text-xs text-blue-600 flex items-center gap-1 font-medium text-center">
                            <i class="bi bi-hourglass-split"></i> Esperando al cliente
                        </span>
                        @elseif($reserva->confirmacion_cliente === 'disputado')
                        <span class="text-xs border border-red-200 text-red-600 bg-red-50 flex items-center gap-1 px-3 py-1 font-medium rounded-xl text-center">
                            <i class="bi bi-exclamation-triangle"></i> En disputa
                        </span>
                        @else
                        <span class="text-xs text-green-600 flex items-center gap-1 font-medium">
                            <i class="bi bi-patch-check-fill"></i> Completada
                        </span>
                        @endif
                    @elseif($reserva->estado === 'cancelada')
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <i class="bi bi-x-circle"></i> Cancelada
                    </span>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="text-center py-16 text-gray-400">
    <i class="bi bi-calendar-x text-5xl mb-4 block"></i>
    <p class="font-medium text-gray-500">No hay reservas</p>
    <p class="text-sm mt-1">
        @if(request('fecha') || request('estado'))
            No hay reservas con los filtros aplicados
        @else
            Aquí aparecerán las reservas cuando los clientes agenden servicios
        @endif
    </p>
</div>
@endforelse

@endsection