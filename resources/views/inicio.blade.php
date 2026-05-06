@extends('layouts.app')
@section('titulo', 'Kliksy — Servicios a domicilio en Florencia, Caquetá')
@section('descripcion', 'Encuentra profesionales verificados de barbería, plomería, electricidad, belleza y más servicios a domicilio en Florencia, Caquetá. Contacto directo por WhatsApp.')
@section('content')

@if(session('success'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-20 right-4 z-50">
    <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg border border-green-600">
        {{ session('success') }}
    </div>
</div>
@endif

{{-- HERO --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 text-center min-h-[calc(100vh-64px)] flex flex-col items-center justify-center">

    <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-xs sm:text-sm px-4 py-1.5 rounded-full mb-4 sm:mb-6">
        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
        Profesionales verificados disponibles ahora
    </div>

    <h1 class="text-3xl sm:text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-4 sm:mb-6">
        El profesional que necesitas,<br>
        <span class="text-blue-500">en tu puerta hoy</span>
    </h1>

    <p class="text-gray-500 text-base sm:text-lg mb-7 sm:mb-10 max-w-2xl mx-auto">
        Barbería, manicura, electricidad, plomería y más. Profesionales reales con reseñas verificadas, historial de trabajos y contacto directo.
    </p>

    <div class="flex justify-center mb-6 sm:mb-10">
        <form action="{{ route('servicios.index') }}" method="GET"
            class="flex items-center bg-white shadow-xl rounded-2xl overflow-hidden w-full max-w-2xl border border-gray-100">
            <div class="px-4 text-gray-400">
                <i class="bi bi-search text-lg"></i>
            </div>
            <input
                type="text"
                name="buscar"
                placeholder="¿Qué servicio necesitas hoy?"
                class="flex-1 py-3.5 sm:py-4 outline-none text-gray-700 text-sm sm:text-base">
            <button type="submit"
                class="bg-blue-600 text-white px-5 sm:px-7 py-2.5 sm:py-3 m-2 rounded-xl hover:bg-blue-700 transition font-medium text-sm sm:text-base">
                Buscar
            </button>
        </form>
    </div>

    {{-- BÚSQUEDAS RÁPIDAS --}}
    <div class="flex flex-wrap justify-center gap-2 text-sm text-gray-500">
        <span class="text-gray-400 text-xs sm:text-sm">Búsquedas frecuentes:</span>
        @foreach(['peluquero', 'manicura', 'plomero', 'electricista', 'aseo', 'masajes', 'entrenador', 'veterinario'] as $tag)
        <a href="{{ route('servicios.index', ['buscar' => $tag]) }}"
            class="bg-white border border-gray-200 hover:border-blue-400 hover:text-blue-600 px-3 py-1 rounded-full text-xs sm:text-sm transition cursor-pointer">
            {{ ucfirst($tag) }}
        </a>
        @endforeach
    </div>

</div>

{{-- ESTADÍSTICAS --}}
@php
    $totalProfesionales = \App\Models\User::where('role_id', 2)->count();
    $totalServicios     = \App\Models\Servicio::count();
    $totalResenas       = \App\Models\Resena::count();
    $totalCompletados   = \App\Models\Solicitud::where('estado', 'completada')->count();
@endphp
<div class="bg-blue-600 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
        <div>
            <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalProfesionales }}+</p>
            <p class="text-blue-200 text-sm mt-1">Profesionales</p>
        </div>
        <div>
            <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalServicios }}+</p>
            <p class="text-blue-200 text-sm mt-1">Servicios publicados</p>
        </div>
        <div>
            <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalCompletados }}+</p>
            <p class="text-blue-200 text-sm mt-1">Servicios completados</p>
        </div>
        <div>
            <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalResenas }}+</p>
            <p class="text-blue-200 text-sm mt-1">Reseñas verificadas</p>
        </div>
    </div>
</div>

{{-- CATEGORÍAS --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-20">

    <div class="text-center mb-8 sm:mb-14">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 sm:mb-3">
            ¿Qué necesitas hoy?
        </h2>
        <p class="text-gray-500 text-sm sm:text-base">
            Elige la categoría y encuentra al profesional ideal cerca de ti
        </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">

        @php
use App\Helpers\CategoriaHelper;
$cats = collect(CategoriaHelper::arbol())->map(fn($v, $k) => [
    'slug'   => $k,
    'nombre' => $v['nombre'],
    'icono'  => $v['icono'],
    'desc'   => collect($v['subs'])->values()->take(3)->implode(', '),
])->values()->take(8)->toArray();
@endphp

        @foreach($cats as $cat)
        <a href="{{ route('servicios.index', ['categoria' => $cat['slug']]) }}"
            class="group block bg-white border border-gray-200 rounded-2xl p-5 sm:p-7 text-center hover:shadow-xl hover:border-blue-300 transition-all duration-200">
            <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 bg-blue-50 group-hover:bg-blue-100 text-blue-500 flex items-center justify-center rounded-2xl text-xl sm:text-2xl transition-colors">
                <i class="bi {{ $cat['icono'] }}"></i>
            </div>
            <h3 class="font-semibold text-sm sm:text-base mb-1">{{ $cat['nombre'] }}</h3>
            <p class="text-gray-400 text-xs hidden sm:block">{{ $cat['desc'] }}</p>
        </a>
        @endforeach

    </div>
</div>

{{-- PROFESIONALES DESTACADOS --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 pb-10 sm:pb-20">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-2 mb-6 sm:mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Mejor calificados</h2>
                <span class="bg-blue-100 text-blue-600 text-xs px-2.5 py-1 rounded-full font-semibold flex items-center gap-1">
                    <i class="bi bi-graph-up-arrow"></i> Esta semana
                </span>
            </div>
            <p class="text-gray-500 text-sm sm:text-base mt-1">
                Profesionales verificados con historial real de trabajos
            </p>
        </div>
        <a href="{{ route('servicios.index') }}"
           class="text-sm text-blue-600 hover:text-blue-700 font-medium self-start sm:self-auto flex items-center gap-1">
            Ver todos <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    {{-- GRID PROFESIONALES --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

    @foreach($profesionales as $profesional)
    @php
        $perfil      = $profesional->perfilProfesional;
        $promedio    = $profesional->promedioCalificacion();
        $completados = \App\Models\Solicitud::where('profesional_id', $profesional->id)
                        ->where('estado', 'completada')->count();
        $verificado  = $perfil?->cedula_frontal && $perfil?->cedula_trasera;
        $categorias  = $perfil?->categorias ?? [];
        $precioDesde = $profesional->servicios->min('precio');
    @endphp

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-200 flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center font-bold text-blue-600 text-base">
                        {{ strtoupper(substr($profesional->name, 0, 2)) }}
                    </div>
                    @if($verificado)
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center border-2 border-white"
                         title="Identidad verificada">
                        <i class="bi bi-check text-white" style="font-size:9px;font-weight:bold;"></i>
                    </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('profesional.publico', $profesional->id) }}"
                       class="font-bold text-gray-800 text-sm hover:text-blue-600 transition block leading-tight">
                        {{ $profesional->name }}
                    </a>
                    <div class="flex items-center gap-1 mt-0.5">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-xs {{ $i <= $promedio ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $promedio > 0 ? number_format($promedio, 1) : 'Nuevo' }}
                            @if($profesional->resenas->count() > 0)
                                ({{ $profesional->resenas->count() }})
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @if($verificado)
            <span class="bg-green-50 text-green-600 text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1 flex-shrink-0">
                <i class="bi bi-patch-check-fill text-xs"></i> Verificado
            </span>
            @endif
        </div>

        {{-- CATEGORÍAS --}}
        @if(is_array($categorias) && count($categorias) > 0)
        <div class="flex flex-wrap gap-1.5 mb-3">
            @foreach(array_slice(is_array($categorias) ? $categorias : [], 0, 3) as $cat)
            <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full">
                {{ ucfirst($cat) }}
            </span>
            @endforeach
            @if(count($categorias) > 3)
            <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full">
                +{{ count($categorias) - 3 }}
            </span>
            @endif
        </div>
        @endif

        {{-- DESCRIPCIÓN --}}
        @if($perfil?->descripcion)
        <p class="text-gray-400 text-xs sm:text-sm mb-4 line-clamp-2 flex-1">
            {{ $perfil->descripcion }}
        </p>
        @endif

        {{-- MÉTRICAS --}}
        <div class="flex items-center gap-3 text-xs text-gray-400 py-3 border-t border-b border-gray-100 mb-4">
            @if($completados > 0)
            <span class="flex items-center gap-1">
                <i class="bi bi-check-circle-fill text-green-500"></i>
                {{ $completados }} completados
            </span>
            @else
            <span class="flex items-center gap-1">
                <i class="bi bi-star text-blue-400"></i>
                Nuevo profesional
            </span>
            @endif
            @if($perfil?->ubicacion)
            <span class="flex items-center gap-1 truncate">
                <i class="bi bi-geo-alt text-gray-300"></i>
                {{ $perfil->ubicacion }}
            </span>
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="flex items-center justify-between">
            @if($precioDesde)
            <div>
                <span class="text-xs text-gray-400">Desde</span>
                <span class="text-blue-600 font-bold text-base ml-1">
                    ${{ number_format($precioDesde, 0, ',', '.') }}
                </span>
            </div>
            @endif
            <a href="{{ route('profesional.publico', $profesional->id) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm px-4 py-2 rounded-full font-medium transition flex items-center gap-1.5">
                Ver perfil <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>

    </div>
    @endforeach
</div>
</div>
{{-- POR QUÉ KLIKSY --}}
<section class="bg-gray-50 border-t border-gray-100 py-12 sm:py-16 w-full">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">¿Por qué elegir Kliksy?</h2>
            <p class="text-gray-500 text-sm sm:text-base">Lo que nos hace diferentes</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl p-6 border border-gray-200 text-center">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="bi bi-patch-check"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Reseñas verificadas</h3>
                <p class="text-gray-500 text-sm">Solo pueden opinar clientes que completaron el servicio. Cero opiniones falsas.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 text-center">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Historial real</h3>
                <p class="text-gray-500 text-sm">Ve cuántos trabajos ha completado cada profesional antes de contactarlo.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 text-center">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Contacto directo</h3>
                <p class="text-gray-500 text-sm">Sin intermediarios ni comisiones. Negocia directamente con el profesional.</p>
            </div>

        </div>
    </div>
</section>

@endsection