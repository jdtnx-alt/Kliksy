@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto py-6 sm:py-8 px-4">

    <h1 class="text-2xl sm:text-3xl font-bold mb-2">Mis Solicitudes</h1>
    <p class="text-gray-500 text-sm sm:text-base mb-6">Historial de servicios solicitados</p>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @forelse($solicitudes as $solicitud)
    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 mb-4 shadow-sm">

        {{-- En móvil: columna. En sm+: fila --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">

            <div class="flex-1 min-w-0">

                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($solicitud->profesional->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $solicitud->profesional->name }}</p>
                        <p class="text-xs text-gray-400">{{ $solicitud->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <p class="text-gray-600 text-sm mb-1">
                    <span class="font-medium">Servicio:</span> {{ $solicitud->servicio->titulo }}
                </p>
                <p class="text-gray-600 text-sm">
                    <span class="font-medium">Precio:</span>
                    ${{ number_format($solicitud->servicio->precio, 0, ',', '.') }}
                </p>

            </div>

            {{-- Badge + botones --}}
            <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3">

                @php
                $colores = [
                    'pendiente'  => 'bg-yellow-100 text-yellow-700',
                    'aceptada'   => 'bg-blue-100 text-blue-700',
                    'completada' => 'bg-green-100 text-green-700',
                    'cancelada'  => 'bg-red-100 text-red-600',
                ];
                @endphp

                <span class="text-xs px-3 py-1 rounded-full font-medium {{ $colores[$solicitud->estado] }} whitespace-nowrap">
                    {{ ucfirst($solicitud->estado) }}
                </span>

                <div class="flex gap-2">
                    @if($solicitud->estado === 'pendiente')
                    <form method="POST" action="{{ route('solicitudes.cancelar', $solicitud->id) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('¿Cancelar esta solicitud?')"
                            class="border border-gray-300 hover:border-red-400 text-gray-600 hover:text-red-500 text-xs sm:text-sm px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg cursor-pointer whitespace-nowrap">
                            Cancelar
                        </button>
                    </form>
                    @endif

                    @if($solicitud->estado === 'completada')
                    <a href="{{ route('profesional.publico', $solicitud->profesional->id) }}?resena=1"
                        class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs sm:text-sm px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg whitespace-nowrap">
                        Dejar reseña
                    </a>
                    @endif
                </div>

            </div>

        </div>

    </div>
    @empty
    <div class="text-center py-12 sm:py-16 text-gray-400">
        <i class="bi bi-inbox text-4xl sm:text-5xl mb-4 block"></i>
        <p class="text-base sm:text-lg">Aún no has solicitado ningún servicio</p>
    </div>
    @endforelse

</div>

@endsection