@extends('layouts.app')
@php
    $tituloSeo = 'Servicios a domicilio';
    $descSeo   = 'Encuentra profesionales verificados cerca de ti en Kliksy.';
    if(request('categoria') && isset($categorias[request('categoria')])) {
        $nombreCat = $categorias[request('categoria')]['nombre'];
        $tituloSeo = $nombreCat . ' a domicilio en Florencia — Kliksy';
        $descSeo   = 'Profesionales de ' . $nombreCat . ' verificados en Florencia, Caquetá. Contacto directo por WhatsApp, sin intermediarios.';
    } elseif(request('buscar')) {
        $tituloSeo = '"' . request('buscar') . '" — Kliksy';
        $descSeo   = 'Resultados para "' . request('buscar') . '" en Kliksy, tu marketplace de servicios a domicilio.';
    }
@endphp
@section('titulo', $tituloSeo)
@section('descripcion', $descSeo)
@section('content')

@php use App\Helpers\CategoriaHelper; @endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    {{-- HEADER --}}
    <div class="mb-5">
        <h2 class="text-2xl sm:text-3xl font-bold mb-1">Servicios</h2>
        <p class="text-gray-500 text-sm sm:text-base">Encuentra el profesional ideal cerca de ti</p>
    </div>

    {{-- BUSCADOR --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-5 shadow-sm">
        <form method="GET" action="{{ route('servicios.index') }}" class="flex gap-3" id="formBuscar">
            <div class="flex-1 relative">
                <i class="bi bi-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" name="buscar" id="buscador"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar servicio o profesional..."
                    class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm bg-gray-50 focus:bg-white transition">
                @if(request('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                @endif
                @if(request('subcategoria'))
                    <input type="hidden" name="subcategoria" value="{{ request('subcategoria') }}">
                @endif
            </div>
            <select name="calificacion" onchange="this.form.submit()"
                class="border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-blue-400 hidden sm:block">
                <option value="">Cualquier calificación</option>
                <option value="5" {{ request('calificacion') == '5' ? 'selected' : '' }}>5 estrellas</option>
                <option value="4" {{ request('calificacion') == '4' ? 'selected' : '' }}>4 o más</option>
                <option value="3" {{ request('calificacion') == '3' ? 'selected' : '' }}>3 o más</option>
                <option value="sin" {{ request('calificacion') == 'sin' ? 'selected' : '' }}>🆕 Sin reseñas</option>
            </select>
        </form>
    </div>

    {{-- LAYOUT: SIDEBAR + GRID --}}
    <div class="flex gap-5 items-start">

        {{-- ══════════════════════════════════
             SIDEBAR CATEGORÍAS (desktop)
        ══════════════════════════════════ --}}
        <aside class="hidden lg:block w-60 flex-shrink-0 sticky top-24">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">

                {{-- Header sidebar --}}
                <div class="bg-blue-600 px-4 py-3 flex items-center gap-2">
                    <i class="bi bi-grid-fill text-white text-sm"></i>
                    <span class="text-white font-semibold text-sm">Categorías</span>
                </div>

                {{-- Todos --}}
                <a href="{{ route('servicios.index', array_filter(['buscar' => request('buscar'), 'calificacion' => request('calificacion')])) }}"
                    class="flex items-center gap-2.5 px-4 py-2.5 text-sm border-b border-gray-100 transition
                    {{ !request('categoria') && !request('subcategoria') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="bi bi-grid text-xs w-4 text-center"></i>
                    Todos los servicios
                </a>

                {{-- Árbol de categorías --}}
                @foreach($categorias as $padreSlug => $padre)
                @php
                    $esPadreActivo = request('categoria') === $padreSlug;
                    $haySubActiva  = request('subcategoria') && isset($padre['subs'][request('subcategoria')]);
                    $abierto       = $esPadreActivo || $haySubActiva;
                @endphp

                <div x-data="{ open: {{ $abierto ? 'true' : 'false' }} }">

                    {{-- Categoría padre --}}
                    <button
                        @click="open = !open"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm border-b border-gray-100 transition text-left cursor-pointer
                        {{ $abierto ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="bi {{ $padre['icono'] }} text-xs w-4 text-center {{ $abierto ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        <span class="flex-1">{{ $padre['nombre'] }}</span>
                        <i class="bi bi-chevron-down text-xs text-gray-300 transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Subcategorías --}}
                    <div x-show="open" x-cloak class="bg-gray-50 border-b border-gray-100">
                        {{-- Ver todos de este padre --}}
                        <a href="{{ route('servicios.index', array_filter(['categoria' => $padreSlug, 'buscar' => request('buscar'), 'calificacion' => request('calificacion')])) }}"
                            class="flex items-center gap-2 pl-9 pr-4 py-2 text-xs transition
                            {{ $esPadreActivo && !request('subcategoria') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600' }}">
                            <i class="bi bi-collection text-gray-300"></i>
                            Ver todos
                        </a>
                        @foreach($padre['subs'] as $subSlug => $subNombre)
                        <a href="{{ route('servicios.index', array_filter(['categoria' => $padreSlug, 'subcategoria' => $subSlug, 'buscar' => request('buscar'), 'calificacion' => request('calificacion')])) }}"
                            class="flex items-center gap-2 pl-9 pr-4 py-2 text-xs transition
                            {{ request('subcategoria') === $subSlug ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-500 hover:text-blue-600' }}">
                            <i class="bi bi-dot text-base leading-none {{ request('subcategoria') === $subSlug ? 'text-blue-500' : 'text-gray-300' }}"></i>
                            {{ $subNombre }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>
        </aside>

        {{-- ══════════════════════════════════
             CONTENIDO PRINCIPAL
        ══════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- CHIPS MÓVIL (solo en mobile, scrollable) --}}
            <div class="lg:hidden flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
                <a href="{{ route('servicios.index', array_filter(['buscar' => request('buscar')])) }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0 transition
                    {{ !request('categoria') ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">
                    Todos
                </a>
                @foreach($categorias as $padreSlug => $padre)
                <a href="{{ route('servicios.index', array_filter(['categoria' => $padreSlug, 'buscar' => request('buscar')])) }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0 transition
                    {{ request('categoria') === $padreSlug ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">
                    <i class="bi {{ $padre['icono'] }} text-xs"></i>
                    {{ $padre['nombre'] }}
                </a>
                @endforeach
            </div>

            {{-- SUBCATEGORÍAS MÓVIL (cuando hay padre activo) --}}
            @if(request('categoria') && isset($categorias[request('categoria')]))
            <div class="lg:hidden flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
                @foreach($categorias[request('categoria')]['subs'] as $subSlug => $subNombre)
                <a href="{{ route('servicios.index', array_filter(['categoria' => request('categoria'), 'subcategoria' => $subSlug, 'buscar' => request('buscar')])) }}"
                    class="flex items-center px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap flex-shrink-0 transition
                    {{ request('subcategoria') === $subSlug ? 'bg-blue-100 text-blue-700 border border-blue-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $subNombre }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- BREADCRUMB / RESULTADO HEADER --}}
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div class="flex items-center gap-2 text-sm text-gray-500 flex-wrap">
                    <i class="bi bi-grid-3x3-gap text-gray-400"></i>
                    <span class="font-medium text-gray-700">{{ $servicios->total() }}</span>
                    <span>servicios</span>

                    @if(request('categoria') && isset($categorias[request('categoria')]))
                        <i class="bi bi-chevron-right text-xs text-gray-300"></i>
                        <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full text-xs font-medium">
                            {{ $categorias[request('categoria')]['nombre'] }}
                        </span>
                    @endif

                    @if(request('subcategoria'))
                        @php
                            $subNombre = '';
                            foreach($categorias as $p) {
                                if(isset($p['subs'][request('subcategoria')])) {
                                    $subNombre = $p['subs'][request('subcategoria')];
                                    break;
                                }
                            }
                        @endphp
                        <i class="bi bi-chevron-right text-xs text-gray-300"></i>
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">
                            {{ $subNombre }}
                        </span>
                    @endif

                    @if(request('buscar'))
                        <i class="bi bi-chevron-right text-xs text-gray-300"></i>
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">
                            "{{ request('buscar') }}"
                        </span>
                    @endif
                </div>

                {{-- Limpiar filtros --}}
                @if(request('categoria') || request('subcategoria') || request('buscar') || request('calificacion'))
                <a href="{{ route('servicios.index') }}"
                    class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition">
                    <i class="bi bi-x-circle"></i> Limpiar filtros
                </a>
                @endif
            </div>

            {{-- GRID DE SERVICIOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

                @foreach($servicios as $servicio)
                @php
                    $promedio    = $servicio->user->promedioCalificacion();
                    $completados = \App\Models\Solicitud::where('profesional_id', $servicio->user->id)
                                    ->where('estado', 'completada')->count();
                    $totalRes    = $servicio->user->resenas->count();

                    // Nombre visible de la subcategoría
                    $catMostrar = '';
                    if ($servicio->subcategoria) {
                        $catMostrar = \App\Helpers\CategoriaHelper::nombre($servicio->subcategoria);
                    } else {
                        $catMostrar = \App\Helpers\CategoriaHelper::nombre($servicio->categoria);
                    }
                @endphp

                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-200 flex flex-col">

    {{-- FOTO O PLACEHOLDER --}}
    @php
$colores = [
    ['bg' => 'bg-blue-50',   'circle' => 'bg-blue-100',   'text' => 'text-blue-600'],
    ['bg' => 'bg-purple-50', 'circle' => 'bg-purple-100', 'text' => 'text-purple-600'],
    ['bg' => 'bg-green-50',  'circle' => 'bg-green-100',  'text' => 'text-green-600'],
    ['bg' => 'bg-amber-50',  'circle' => 'bg-amber-100',  'text' => 'text-amber-600'],
    ['bg' => 'bg-rose-50',   'circle' => 'bg-rose-100',   'text' => 'text-rose-600'],
    ['bg' => 'bg-teal-50',   'circle' => 'bg-teal-100',   'text' => 'text-teal-600'],
    ['bg' => 'bg-orange-50', 'circle' => 'bg-orange-100', 'text' => 'text-orange-600'],
    ['bg' => 'bg-indigo-50', 'circle' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
];
$color = $colores[crc32($servicio->user->name ?? 'U') % count($colores)];
@endphp
<div class="relative">
    @php
    $fotosServIdx = $servicio->fotos ?? ($servicio->foto ? [$servicio->foto] : []);
    if (empty($fotosServIdx)) {
        $perfilFotos = $servicio->user->perfilProfesional?->fotos;
        if ($perfilFotos && $perfilFotos->count() > 0) {
            $fotosServIdx = $perfilFotos->pluck('ruta')->toArray();
        }
    }
@endphp
@if(count($fotosServIdx) > 0)
    <img src="{{ asset('storage/' . $fotosServIdx[0]) }}"
        class="w-full h-40 object-cover object-center"
        alt="{{ $servicio->titulo }}">
    @if(count($fotosServIdx) > 1)
    <span class="absolute top-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded-lg flex items-center gap-1">
        <i class="bi bi-images text-xs"></i> {{ count($fotosServIdx) }}
    </span>
    @endif
@else
        <div class="w-full h-40 {{ $color['bg'] }} flex flex-col items-center justify-center gap-1">
            <div class="w-14 h-14 rounded-full {{ $color['circle'] }} flex items-center justify-center {{ $color['text'] }} font-bold text-xl">
                {{ strtoupper(substr($servicio->user->name ?? 'U', 0, 2)) }}
            </div>
            <span class="text-xs text-gray-400">Sin foto aún</span>
        </div>
    @endif

    {{-- BADGE VERIFICADO O NUEVO --}}
    @if($completados >= 10)
    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold flex items-center gap-1">
        <i class="bi bi-patch-check-fill" style="font-size:10px"></i> Verificado
    </span>
    @elseif($completados === 0)
    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2.5 py-1 rounded-full font-semibold">
        Nuevo
    </span>
    @endif
</div>

    {{-- CUERPO --}}
    <div class="p-4 flex flex-col flex-1">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-full {{ $color['circle'] }} flex items-center justify-center {{ $color['text'] }} font-bold text-xs flex-shrink-0">
    {{ strtoupper(substr($servicio->user->name ?? 'U', 0, 2)) }}
</div>
                <div class="min-w-0">
                    <a href="{{ route('profesional.publico', $servicio->user->id) }}"
                       class="font-semibold text-gray-800 text-sm hover:text-blue-600 transition block truncate">
                        {{ $servicio->user->name ?? 'Profesional' }}
                    </a>
                    <div class="flex items-center gap-1">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-xs {{ $i <= $promedio ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ $promedio > 0 ? number_format($promedio, 1) : 'Nuevo' }}
                            @if($totalRes > 0)({{ $totalRes }})@endif
                        </span>
                    </div>
                </div>
            </div>
            <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full flex-shrink-0 ml-2 font-medium">
                {{ $catMostrar }}
            </span>
        </div>

        {{-- SERVICIO --}}
        <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $servicio->titulo }}</h3>

        {{-- MÉTRICAS --}}
        <div class="flex items-center gap-3 text-xs text-gray-400 mb-3 flex-1">
            <span class="flex items-center gap-1">
                @if($completados > 0)
                    <i class="bi bi-check-circle-fill text-green-500"></i>{{ $completados }} completados
                @else
                    <i class="bi bi-star text-blue-400"></i>Nuevo en Kliksy
                @endif
            </span>
            @if($servicio->user->perfilProfesional?->ubicacion)
            <span class="flex items-center gap-1 truncate">
                <i class="bi bi-geo-alt text-gray-300"></i>
                {{ $servicio->user->perfilProfesional->ubicacion }}
            </span>
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <span class="text-blue-600 font-bold text-lg">
                ${{ number_format($servicio->precio, 0, ',', '.') }}
            </span>

            @auth
    @if(auth()->user()->role_id === 1)
    <a href="{{ route('reservas.create', [$servicio->user->id, 'servicio_id' => $servicio->id]) }}"
        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-full flex items-center gap-1.5 font-medium transition">
        <i class="bi bi-calendar-check"></i> Reservar
    </a>
    @endif
@else
<button onclick="openLogin()"
    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-full cursor-pointer flex items-center gap-1.5 font-medium transition">
    <i class="bi bi-calendar-check"></i> Reservar
</button>
@endauth
        </div>

    </div>
</div>
                @endforeach

                {{-- VACÍO --}}
                @if($servicios->isEmpty())
                <div class="col-span-1 sm:col-span-2 xl:col-span-3 text-center py-16 text-gray-400">
                    <i class="bi bi-search text-5xl mb-4 block"></i>
                    <p class="text-lg font-medium">No hay servicios en esta categoría</p>
                    <p class="text-sm mt-1">Intenta con otra categoría o busca por nombre</p>
                    <a href="{{ route('servicios.index') }}" class="mt-4 inline-block text-blue-600 hover:underline text-sm">
                        Ver todos los servicios
                    </a>
                </div>
                @endif

            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-8 flex justify-end">
                {{ $servicios->links() }}
            </div>

        </div>{{-- fin contenido --}}
    </div>{{-- fin layout --}}

</div>

@push('scripts')
<script>

// Búsqueda con debounce
document.getElementById('buscador').addEventListener('input', function () {
    clearTimeout(this._timer);
    this._timer = setTimeout(() => document.getElementById('formBuscar').submit(), 800);
});
</script>
@endpush

@endsection