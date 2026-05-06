@extends('layouts.app')
@section('titulo', 'Mis reservas — Kliksy')
@section('content')

<div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mis reservas</h1>
        <p class="text-gray-400 text-sm mt-1">Historial de todas tus reservas</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    @forelse($reservas as $reserva)
    <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-4 hover:border-blue-200 transition">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($reserva->profesional->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $reserva->profesional->name }}</p>
                    <p class="text-xs text-gray-400">{{ $reserva->servicio->titulo }}</p>
                </div>
            </div>
            {{-- BADGE ESTADO --}}
            @php
                $badgeClass = match($reserva->estado) {
                    'pendiente'  => 'bg-yellow-100 text-yellow-700',
                    'confirmada' => 'bg-blue-100 text-blue-700',
                    'completada' => 'bg-green-100 text-green-700',
                    'cancelada'  => 'bg-red-100 text-red-500',
                    default      => 'bg-gray-100 text-gray-500',
                };
                $badgeIcon = match($reserva->estado) {
                    'pendiente'  => 'bi-hourglass-split',
                    'confirmada' => 'bi-check-circle',
                    'completada' => 'bi-patch-check-fill',
                    'cancelada'  => 'bi-x-circle',
                    default      => 'bi-circle',
                };
            @endphp
            <span class="text-xs px-2.5 py-1 rounded-full font-medium flex items-center gap-1 flex-shrink-0 {{ $badgeClass }}">
                <i class="bi {{ $badgeIcon }}"></i>
                {{ ucfirst($reserva->estado) }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-gray-500 mb-4">
            <div class="flex items-center gap-1.5">
                <i class="bi bi-calendar3 text-blue-400"></i>
                {{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('D MMM YYYY') }}
            </div>
            <div class="flex items-center gap-1.5">
                <i class="bi bi-clock text-blue-400"></i>
                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i A') }} –
                {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('g:i A') }}
            </div>
            <div class="flex items-center gap-1.5">
                <i class="bi bi-cash text-green-400"></i>
                ${{ number_format($reserva->monto, 0, ',', '.') }} COP
            </div>
        </div>

        @if(in_array($reserva->estado, ['pendiente', 'confirmada', 'completada']))
            @if(in_array($reserva->confirmacion_cliente, ['pendiente', null]))
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl mt-3 mb-2">
                @if($reserva->estado === 'completada')
                <p class="text-sm font-semibold text-blue-800 mb-1">¡El profesional marcó este servicio como completado!</p>
                <p class="text-xs text-blue-600 mb-3">Tienes hasta el {{ \Carbon\Carbon::parse($reserva->liberacion_cliente_at)->format('d/m/Y h:i A') }} para confirmar. De lo contrario, el pago se liberará automáticamente.</p>
                @else
                <p class="text-sm font-semibold text-blue-800 mb-1">Confirma tu servicio</p>
                <p class="text-xs text-blue-600 mb-3">Si el profesional ya realizó el trabajo o no se presentó, repórtalo aquí.</p>
                @endif
                <div class="flex flex-col sm:flex-row gap-2">
                    <form method="POST" action="{{ route('reservas.confirmar', $reserva->id) }}" class="flex-1">
                        @csrf
                        <button type="submit" onclick="return confirm('¿Confirmas que el servicio se completó correctamente?')" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                            <i class="bi bi-check-lg"></i> Sí, está completado
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('modal-disputa-{{ $reserva->id }}').classList.remove('hidden')" class="flex-1 border border-red-200 text-red-500 hover:bg-red-50 text-xs font-semibold px-4 py-2 rounded-xl transition">
                        <i class="bi bi-exclamation-triangle"></i> Reportar problema
                    </button>
                </div>
            </div>

            {{-- MODAL DISPUTA --}}
            <div id="modal-disputa-{{ $reserva->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-800">Reportar no completado</h3>
                        <button type="button" onclick="document.getElementById('modal-disputa-{{ $reserva->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('reservas.disputar', $reserva->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">¿Qué pasó?</label>
                            <textarea name="motivo" rows="4" required class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400" placeholder="Explica detalladamente por qué consideras que el servicio no fue completado..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-xl transition">
                            Enviar reporte al administrador
                        </button>
                    </form>
                </div>
            </div>

            @elseif($reserva->confirmacion_cliente === 'disputado')
            <div class="bg-orange-50 border border-orange-200 p-3 rounded-xl mt-3 flex items-start gap-2">
                <i class="bi bi-exclamation-circle-fill text-orange-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-orange-800">En revisión (Disputa)</p>
                    <p class="text-xs text-orange-600">Has reportado este servicio. El administrador está revisando tu caso. Te contactaremos pronto.</p>
                </div>
            </div>
            @endif

            @if($reserva->confirmacion_cliente === 'confirmado' || $reserva->disputa?->estado === 'resuelto_cliente' || $reserva->disputa?->estado === 'resuelto_profesional')
            @php
                $yaReseno = \App\Models\Resena::where('cliente_id', auth()->id())
                    ->where('profesional_id', $reserva->profesional_id)
                    ->exists();
            @endphp
            @if(!$yaReseno)
            <a href="{{ route('profesional.publico', $reserva->profesional_id) }}?tab=resenas"
                class="w-full flex items-center justify-center gap-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-4 py-2 rounded-xl transition mt-3">
                <i class="bi bi-star-fill"></i> Dejar reseña
            </a>
            @else
            <span class="text-xs text-green-600 flex items-center gap-1 mt-3">
                <i class="bi bi-check-circle-fill"></i> Reseña enviada
            </span>
            @endif
            @endif
        @endif

        @if($reserva->estado === 'pendiente' || $reserva->estado === 'confirmada')
        <form method="POST" action="{{ route('reservas.cancelar', $reserva->id) }}">
            @csrf
            <button type="submit"
                onclick="return confirm('¿Seguro que quieres cancelar esta reserva?')"
                class="text-xs text-red-400 hover:text-red-600 flex items-center gap-1 transition cursor-pointer">
                <i class="bi bi-x-circle"></i> Cancelar reserva
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <i class="bi bi-calendar-x text-5xl mb-4 block"></i>
        <p class="font-medium text-gray-500">Aún no tienes reservas</p>
        <p class="text-sm mt-1">Explora profesionales y reserva tu primer servicio</p>
        <a href="{{ route('servicios.index') }}"
            class="mt-4 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
            <i class="bi bi-search"></i> Explorar servicios
        </a>
    </div>
    @endforelse

</div>
@endsection