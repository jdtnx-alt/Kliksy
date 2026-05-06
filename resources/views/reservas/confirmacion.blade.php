@extends('layouts.app')
@section('titulo', 'Reserva confirmada — Kliksy')
@section('content')

<div class="max-w-lg mx-auto px-4 py-12 text-center">

    {{-- ÍCONO ÉXITO --}}
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="bi bi-check-lg text-green-500 text-4xl"></i>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">¡Reserva confirmada!</h1>
    <p class="text-gray-400 text-sm mb-8">Tu pago fue procesado y la reserva está confirmada.</p>

    {{-- DETALLES --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 text-left mb-6">
        <h3 class="font-bold text-gray-800 mb-4 text-sm">Detalles de tu reserva</h3>
        <div class="flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($reserva->profesional->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $reserva->profesional->name }}</p>
                    <p class="text-xs text-gray-400">{{ $reserva->servicio->titulo }}</p>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-3 flex flex-col gap-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-blue-400"></i> Fecha
                    </span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="bi bi-clock text-blue-400"></i> Hora
                    </span>
                    <span class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i A') }} –
                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('g:i A') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="bi bi-clock text-blue-400"></i> Duración
                    </span>
                    <span class="font-medium text-gray-800">{{ $reserva->servicio->duracion }} min</span>
                </div>
                <div class="flex justify-between border-t border-gray-100 pt-2 mt-1">
                    <span class="font-bold text-gray-800">Total pagado</span>
                    <span class="font-bold text-green-600">${{ number_format($reserva->monto, 0, ',', '.') }} COP</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ESTADO --}}
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <i class="bi bi-patch-check-fill text-green-500 text-xl flex-shrink-0"></i>
        <div class="text-left">
            <p class="font-semibold text-green-800 text-sm">Pago confirmado</p>
            <p class="text-green-600 text-xs mt-0.5">El profesional ha sido notificado de tu reserva</p>
        </div>
    </div>

    {{-- ACCIONES --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('reservas.mis') }}"
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-semibold text-sm transition flex items-center justify-center gap-2">
            <i class="bi bi-calendar-check"></i> Ver mis reservas
        </a>
        <a href="{{ route('inicio') }}"
            class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-600 py-3 rounded-2xl font-semibold text-sm transition flex items-center justify-center gap-2">
            <i class="bi bi-house"></i> Ir al inicio
        </a>
    </div>

</div>
@endsection