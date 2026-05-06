@extends('layouts.profesional')
@section('title', 'Reseñas')
@section('page_title', 'Mis Reseñas')

@section('content')
@php
    $resenas        = auth()->user()->resenas()->with('cliente')->latest()->get();
    $promedio       = $resenas->count() ? round($resenas->avg('calificacion'), 1) : 0;
@endphp

<div class="max-w-3xl">

    {{-- RESUMEN --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-5 sm:gap-8 items-center">
            <div class="text-center flex-shrink-0">
                <p class="text-6xl font-bold text-gray-800">{{ $promedio }}</p>
                <div class="flex gap-1 justify-center mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $promedio ? 'text-yellow-400' : 'text-gray-200' }} text-xl">★</span>
                    @endfor
                </div>
                <p class="text-gray-400 text-xs mt-1.5">{{ $resenas->count() }} reseñas verificadas</p>
            </div>
            <div class="flex-1 w-full">
                @foreach([5,4,3,2,1] as $estrella)
                @php
                    $cantidad   = $resenas->where('calificacion', $estrella)->count();
                    $porcentaje = $resenas->count() ? ($cantidad / $resenas->count()) * 100 : 0;
                @endphp
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-xs text-gray-500 w-2 flex-shrink-0">{{ $estrella }}</span>
                    <span class="text-yellow-400 text-xs flex-shrink-0">★</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <span class="text-xs text-gray-400 w-4 text-right flex-shrink-0">{{ $cantidad }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- LISTA --}}
    @forelse($resenas as $resena)
    <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($resena->cliente->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $resena->cliente->name }}</p>
                    <p class="text-xs text-gray-400">{{ $resena->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-200' }} text-sm">★</span>
                @endfor
            </div>
        </div>
        <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $resena->comentario }}</p>

        @foreach($resena->respuestas as $respuesta)
        <div class="ml-4 sm:ml-6 border-l-2 border-blue-100 pl-3 mt-3 mb-2 bg-blue-50/40 rounded-r-xl py-2">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr($respuesta->user->name, 0, 2)) }}
                </div>
                <p class="font-semibold text-gray-800 text-xs">{{ $respuesta->user->name }}</p>
                @if($respuesta->user_id === auth()->id())
                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Tú</span>
                @endif
                <p class="text-xs text-gray-400">{{ $respuesta->created_at->diffForHumans() }}</p>
            </div>
            <p class="text-gray-600 text-sm">{{ $respuesta->contenido }}</p>
        </div>
        @endforeach

        <div x-data="{ abierto: false }" class="mt-3">
            <button @click="abierto = !abierto" class="flex items-center gap-1.5 text-gray-400 hover:text-blue-500 text-xs cursor-pointer transition">
                <i class="bi bi-chat"></i> Responder
            </button>
            <div x-show="abierto" x-cloak class="mt-3">
                <form method="POST" action="{{ route('resenas.responder', $resena->id) }}">
                    @csrf
                    <textarea name="contenido" rows="2" placeholder="Escribe tu respuesta..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50"></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-2 rounded-lg cursor-pointer transition">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center text-gray-400">
        <i class="bi bi-star text-5xl mb-4 block"></i>
        <p class="font-medium text-gray-500">Aún no tienes reseñas</p>
        <p class="text-sm mt-1">Cuando completes servicios, tus clientes podrán dejarte reseñas</p>
    </div>
    @endforelse

</div>
@endsection