@extends('layouts.app')
@php
    $serviciosNombres = $usuario->servicios->pluck('titulo')->take(3)->implode(', ');
    $tituloSeo = $usuario->name . ' — Profesional a domicilio | Kliksy';
    $descSeo   = $usuario->name . ' ofrece servicios a domicilio en Florencia, Caquetá: ' . $serviciosNombres . '. Reseñas verificadas y contacto directo por WhatsApp.';
@endphp
@section('titulo', $tituloSeo)
@section('descripcion', $descSeo)
@section('content')

@php
    $completados     = \App\Models\Reserva::where('profesional_id', $usuario->id)->where('estado', 'completada')->count();
    $canceladas      = \App\Models\Reserva::where('profesional_id', $usuario->id)->where('estado', 'cancelada')->count();
    $totalSolicitudes = $completados + $canceladas;
    $tasaExito       = $totalSolicitudes > 0 ? round(($completados / $totalSolicitudes) * 100) : null;
    $clientesUnicos  = \App\Models\Reserva::where('profesional_id', $usuario->id)->where('estado', 'completada')->distinct('cliente_id')->count('cliente_id');
    $clientesRepeat  = \App\Models\Reserva::where('profesional_id', $usuario->id)->where('estado', 'completada')->select('cliente_id')->groupBy('cliente_id')->havingRaw('COUNT(*) > 1')->count();
    $retencion       = $clientesUnicos > 0 ? round(($clientesRepeat / $clientesUnicos) * 100) : null;
    $esVerificado    = $completados >= 10;
@endphp

<div class="max-w-6xl mx-auto py-6 sm:py-8 px-4 sm:px-6 pb-24 lg:pb-8">

    <a href="{{ url()->previous() }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-800 mb-6 text-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>

    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- IZQUIERDA --}}
        <div class="flex-1 w-full min-w-0">

            {{-- TARJETA PROFESIONAL --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-6">

                <div class="flex flex-col sm:flex-row items-start gap-4 mb-5">

                    {{-- AVATAR --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl sm:text-3xl">
                            {{ strtoupper(substr($usuario->name, 0, 2)) }}
                        </div>
                        @if($esVerificado)
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white" title="Verificado: 10+ servicios completados">
                            <i class="bi bi-check text-white" style="font-size:11px;font-weight:bold;"></i>
                        </div>
                        @endif
                    </div>

                    {{-- INFO --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $usuario->name }}</h1>
                            @if($esVerificado)
                            <span class="bg-green-100 text-green-700 text-xs px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1">
                                <i class="bi bi-patch-check-fill"></i> Verificado
                            </span>
                            @endif
                            @if($usuario->perfilProfesional?->en_vacaciones)
<span class="bg-orange-100 text-orange-700 text-xs px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1">
    <i class="bi bi-moon-fill"></i> En vacaciones
</span>
@endif
                        </div>

                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $promedio ? 'text-yellow-400' : 'text-gray-200' }} text-base">★</span>
                                @endfor
                            </div>
                            <span class="text-gray-600 text-sm font-medium">{{ $promedio > 0 ? number_format($promedio, 1) : '0' }}</span>
                            <span class="text-gray-400 text-sm">({{ $usuario->resenas->count() }} reseñas)</span>
                        </div>

                        @if($usuario->perfilProfesional?->categorias)
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach(is_array($usuario->perfilProfesional->categorias) ? $usuario->perfilProfesional->categorias : json_decode($usuario->perfilProfesional->categorias ?? '[]', true) as $cat)
                            <span class="text-xs bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1 rounded-full">
                                {{ ucfirst($cat) }}
                            </span>
                            @endforeach
                        </div>
                        @endif

                        <div class="flex flex-wrap gap-3 sm:gap-4 text-sm text-gray-500">
                            @if($usuario->perfilProfesional?->ubicacion)
                            <span class="flex items-center gap-1"><i class="bi bi-geo-alt text-blue-400"></i>{{ $usuario->perfilProfesional->ubicacion }}</span>
                            @endif
                            @if($usuario->perfilProfesional?->experiencia)
                            <span class="flex items-center gap-1"><i class="bi bi-briefcase text-blue-400"></i>{{ $usuario->perfilProfesional->experiencia }}</span>
                            @endif
                        </div>

                        @if($usuario->perfilProfesional?->descripcion)
                        <p class="text-gray-500 text-sm mt-3 leading-relaxed">
                            {{ $usuario->perfilProfesional->descripcion }}
                        </p>
                        @endif
                    </div>
                </div>

                {{-- MÉTRICAS DEL PROFESIONAL --}}
                {{-- BOTÓN COMPARTIR --}}
<div class="flex justify-end mt-3">
    <button onclick="copiarPerfil()"
        id="btnCompartir"
        class="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-4 py-2 rounded-xl transition cursor-pointer bg-white">
        <i class="bi bi-share" id="iconoCompartir"></i>
        <span id="textoCompartir">Compartir perfil</span>
    </button>
</div>
@auth
    @if(auth()->user()->role_id === 1)
    @php $yaReportoProfesional = \App\Models\ReporteServicio::where('profesional_id', $usuario->id)->where('user_id', auth()->id())->where('tipo','profesional')->exists(); @endphp
    <div x-data="{ abierto: false }" class="flex justify-end mt-2">
        @if(!$yaReportoProfesional)
        <button @click="abierto = !abierto"
            class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-red-500 transition cursor-pointer">
            <i class="bi bi-flag"></i> Reportar profesional
        </button>
        <div x-show="abierto" x-cloak
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            @click.self="abierto = false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
                <h3 class="font-bold text-gray-800 mb-1">Reportar a {{ explode(' ', $usuario->name)[0] }}</h3>
                <p class="text-xs text-gray-400 mb-4">Describe el motivo de tu reporte. Lo revisaremos en menos de 24 horas.</p>
                <form method="POST" action="{{ route('profesional.reportar', $usuario->id) }}">
                    @csrf
                    <textarea name="motivo" rows="3" required placeholder="Ej: Comportamiento inapropiado, información falsa..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-gray-50 mb-4 resize-none"></textarea>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="abierto = false"
                            class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl border border-gray-200 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm px-5 py-2 rounded-xl cursor-pointer transition">
                            Enviar reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <span class="text-xs text-gray-300 flex items-center gap-1">
            <i class="bi bi-flag"></i> Ya reportaste a este profesional
        </span>
        @endif
    </div>
    @endif
@endauth
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-gray-100">
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        <p class="text-xl font-bold text-gray-800">{{ $completados }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Completados</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        <p class="text-xl font-bold text-gray-800">{{ $usuario->resenas->count() }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Reseñas</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        @if($tasaExito !== null)
                        <p class="text-xl font-bold {{ $tasaExito >= 80 ? 'text-green-600' : ($tasaExito >= 60 ? 'text-yellow-600' : 'text-red-500') }}">{{ $tasaExito }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tasa de éxito</p>
                        @else
                        <p class="text-xl font-bold text-gray-300">—</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tasa de éxito</p>
                        @endif
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        @if($retencion !== null)
                        <p class="text-xl font-bold text-blue-600">{{ $retencion }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Clientes que vuelven</p>
                        @else
                        <p class="text-xl font-bold text-gray-300">—</p>
                        <p class="text-xs text-gray-500 mt-0.5">Clientes que vuelven</p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- TABS --}}
            <div x-data="{ tab: 'servicios' }">

                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                    <button @click="tab='servicios'"
                        :class="tab==='servicios' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500'"
                        class="px-4 sm:px-6 py-3 border-b-2 font-medium cursor-pointer text-sm sm:text-base whitespace-nowrap">
                        Servicios ({{ $usuario->servicios->count() }})
                    </button>
                    <button @click="tab='resenas'"
                        :class="tab==='resenas' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500'"
                        class="px-4 sm:px-6 py-3 border-b-2 font-medium cursor-pointer text-sm sm:text-base whitespace-nowrap">
                        Reseñas ({{ $usuario->resenas->count() }})
                    </button>
                    <button @click="tab='negocio'"
                        :class="tab==='negocio' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500'"
                        class="px-4 sm:px-6 py-3 border-b-2 font-medium cursor-pointer text-sm sm:text-base whitespace-nowrap">
                        Negocio
                    </button>
                </div>

                {{-- TAB: SERVICIOS --}}
                <div x-show="tab === 'servicios'" x-cloak>
                    @php
    $coloresPub = [
        ['bg' => 'bg-blue-50',   'circle' => 'bg-blue-100',   'text' => 'text-blue-600'],
        ['bg' => 'bg-purple-50', 'circle' => 'bg-purple-100', 'text' => 'text-purple-600'],
        ['bg' => 'bg-green-50',  'circle' => 'bg-green-100',  'text' => 'text-green-600'],
        ['bg' => 'bg-amber-50',  'circle' => 'bg-amber-100',  'text' => 'text-amber-600'],
        ['bg' => 'bg-rose-50',   'circle' => 'bg-rose-100',   'text' => 'text-rose-600'],
        ['bg' => 'bg-teal-50',   'circle' => 'bg-teal-100',   'text' => 'text-teal-600'],
    ];
    $colorPub = $coloresPub[crc32($usuario->name ?? 'U') % count($coloresPub)];
@endphp

@forelse($usuario->servicios as $servicio)
<div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mb-3 hover:border-blue-200 hover:shadow-sm transition">

    <div class="p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full font-medium">
                    {{ \App\Helpers\CategoriaHelper::nombre($servicio->subcategoria ?: $servicio->categoria) }}
                </span>
            </div>
            <h3 class="font-semibold text-gray-800 text-sm sm:text-base">{{ $servicio->titulo }}</h3>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5">{{ $servicio->descripcion }}</p>
            @php
                $fotosPublico = $servicio->fotos ?? ($servicio->foto ? [$servicio->foto] : []);
                if (empty($fotosPublico)) {
                    $perfilFotos = $usuario->perfilProfesional?->fotos;
                    if ($perfilFotos && $perfilFotos->count() > 0) {
                        $fotosPublico = $perfilFotos->pluck('ruta')->toArray();
                    }
                }
            @endphp
@if(count($fotosPublico) > 0)
<div x-data="{
    lightbox: false,
    indice: 0,
    fotos: {{ json_encode(array_map(fn($f) => asset('storage/' . $f), $fotosPublico)) }},
    get total() { return this.fotos.length },
    anterior() { this.indice = (this.indice - 1 + this.total) % this.total },
    siguiente() { this.indice = (this.indice + 1) % this.total }
}">
    <button @click="lightbox = true; indice = 0"
        class="mt-2 flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-700 transition cursor-pointer">
        <i class="bi bi-images"></i>
        Ver {{ count($fotosPublico) }} foto{{ count($fotosPublico) > 1 ? 's' : '' }}
    </button>
    <div x-show="lightbox"
        class="fixed inset-0 bg-black/90 z-[300] flex items-center justify-center p-4"
        @click="lightbox = false"
        @keydown.escape.window="lightbox = false"
        style="display:none">
        <button @click="lightbox = false"
            class="absolute top-4 right-5 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center text-lg transition z-10">✕</button>
        <div x-show="total > 1"
            class="absolute top-4 left-1/2 -translate-x-1/2 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full">
            <span x-text="indice + 1"></span> / <span x-text="total"></span>
        </div>
        <img :src="fotos[indice]"
            class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain"
            @click.stop>
        <button x-show="total > 1" @click.stop="anterior()"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">
            <i class="bi bi-chevron-left text-lg"></i>
        </button>
        <button x-show="total > 1" @click.stop="siguiente()"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">
            <i class="bi bi-chevron-right text-lg"></i>
        </button>
        <div x-show="total > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2"
            @click.stop>
            <template x-for="(foto, i) in fotos" :key="i">
                <img :src="foto" @click="indice = i"
                    :class="indice === i ? 'ring-2 ring-white opacity-100' : 'opacity-50 hover:opacity-80'"
                    class="w-12 h-12 object-cover rounded-lg cursor-pointer transition">
            </template>
        </div>
    </div>
</div>
@endif
        </div>
        <div class="flex items-center justify-between sm:justify-end gap-3 flex-shrink-0">
            <span class="text-blue-600 font-bold text-base sm:text-lg">
                ${{ number_format($servicio->precio, 0, ',', '.') }}
            </span>
            @auth
    @if(auth()->user()->role_id === 1)
    <a href="{{ route('reservas.create', [$usuario->id, 'servicio_id' => $servicio->id]) }}"
        class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm px-4 py-2 rounded-full flex items-center gap-1.5 font-medium transition">
        <i class="bi bi-calendar-check"></i>
        <span class="hidden sm:inline">Reservar</span>
    </a>
    @endif
@else
<button onclick="openLogin()"
    class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm px-4 py-2 rounded-full flex items-center gap-1.5 cursor-pointer font-medium transition">
    <i class="bi bi-calendar-check"></i>
    <span class="hidden sm:inline">Reservar</span>
</button>
@endauth
        </div>
    </div>
    @auth
    @if(auth()->user()->role_id === 1)
    @php $yaReportoServicio = \App\Models\ReporteServicio::where('servicio_id', $servicio->id)->where('user_id', auth()->id())->where('tipo','servicio')->exists(); @endphp
    <div x-data="{ abierto: false }" class="px-4 pb-3">
        @if(!$yaReportoServicio)
        <button @click="abierto = !abierto"
            class="flex items-center gap-1 text-gray-300 hover:text-red-400 text-xs cursor-pointer transition">
            <i class="bi bi-flag"></i> Reportar servicio
        </button>
        <div x-show="abierto" x-cloak
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            @click.self="abierto = false">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
                <h3 class="font-bold text-gray-800 mb-1">Reportar servicio</h3>
                <p class="text-sm text-gray-500 mb-1 font-medium">{{ $servicio->titulo }}</p>
                <p class="text-xs text-gray-400 mb-4">Describe el motivo del reporte.</p>
                <form method="POST" action="{{ route('servicios.reportar', $servicio->id) }}">
                    @csrf
                    <textarea name="motivo" rows="3" required placeholder="Ej: Precio engañoso, descripción falsa..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-gray-50 mb-4 resize-none"></textarea>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="abierto = false"
                            class="text-sm text-gray-500 px-4 py-2 rounded-xl border border-gray-200 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm px-5 py-2 rounded-xl cursor-pointer transition">
                            Enviar reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <span class="text-xs text-gray-300 flex items-center gap-1">
            <i class="bi bi-flag"></i> Ya reportaste este servicio
        </span>
        @endif
    </div>
    @endif
@endauth
</div>
@empty
<div class="text-center py-10 text-gray-400">
    <i class="bi bi-scissors text-4xl mb-3 block"></i>
    <p class="text-sm">Este profesional aún no tiene servicios registrados.</p>
</div>
@endforelse
                </div>

                {{-- TAB: RESEÑAS --}}
                <div x-show="tab === 'resenas'" x-cloak>

                    @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                    @endif

                    {{-- RESUMEN --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-5">
                        <div class="flex flex-col sm:flex-row gap-5 sm:gap-8 items-center">
                            <div class="text-center flex-shrink-0">
                                <p class="text-5xl sm:text-6xl font-bold text-gray-800">{{ number_format($promedio, 1) }}</p>
                                <div class="flex gap-1 justify-center mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $promedio ? 'text-yellow-400' : 'text-gray-200' }} text-xl">★</span>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm mt-1">{{ $usuario->resenas->count() }} reseñas verificadas</p>
                            </div>
                            <div class="flex-1 w-full">
                                @foreach([5,4,3,2,1] as $estrella)
                                @php
                                    $total      = $usuario->resenas->count();
                                    $cantidad   = $usuario->resenas->where('calificacion', $estrella)->count();
                                    $porcentaje = $total ? ($cantidad / $total) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs text-gray-500 w-2 flex-shrink-0">{{ $estrella }}</span>
                                    <span class="text-yellow-400 text-xs flex-shrink-0">★</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-yellow-400 h-2 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 w-4 text-right flex-shrink-0">{{ $cantidad }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @auth
    @if($puedeResenar && !$yaReseno)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-5">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-star text-blue-500"></i> Deja tu reseña
        </h3>
        <form method="POST" action="{{ route('resenas.store', $usuario->id) }}">
            @csrf
            <input type="hidden" name="servicio_id" value="{{ $servicioCompletado }}">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Calificación</label>
                <div class="flex gap-2" x-data="{ rating: 0 }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                        @click="rating = {{ $i }}"
                        :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                        class="text-3xl cursor-pointer transition-all hover:scale-110"
                        x-on:click="document.getElementById('calificacion').value = {{ $i }}">★</button>
                    @endfor
                    <input type="hidden" name="calificacion" id="calificacion" value="0">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Comentario</label>
                <textarea name="comentario" rows="3"
                    placeholder="Comparte tu experiencia..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl cursor-pointer text-sm font-medium transition">
                    Publicar reseña
                </button>
            </div>
        </form>
    </div>
    @elseif($yaReseno)
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> Ya dejaste una reseña a este profesional.
    </div>
    @elseif(auth()->user()->role_id === 1)
    <div class="bg-gray-50 border border-gray-200 text-gray-500 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
        <i class="bi bi-info-circle"></i> Solo puedes reseñar si completaste un servicio con este profesional.
    </div>
    @endif
@endauth

                    {{-- LISTA RESEÑAS --}}
                    @forelse($usuario->resenas as $resena)
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 mb-3">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($resena->cliente->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $resena->cliente->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $resena->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex gap-0.5 self-start sm:self-auto">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-200' }} text-sm">★</span>
                                @endfor
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $resena->comentario }}</p>

                        @foreach($resena->respuestas as $respuesta)
                        <div class="ml-4 sm:ml-6 border-l-2 border-blue-100 pl-3 sm:pl-4 mt-3 mb-2 bg-blue-50/50 rounded-r-xl py-2">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($respuesta->user->name, 0, 2)) }}
                                </div>
                                <p class="font-semibold text-gray-800 text-xs sm:text-sm">{{ $respuesta->user->name }}</p>
                                @if($respuesta->user_id === $usuario->id)
                                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Profesional</span>
                                @endif
                                <p class="text-xs text-gray-400">{{ $respuesta->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-gray-600 text-sm">{{ $respuesta->contenido }}</p>
                        </div>
                        @endforeach

                        @auth
                        @php
                            $puedeReportar = false;
                            if(auth()->user()->role_id === 1 && $resena->cliente_id !== auth()->id()) $puedeReportar = true;
                            if(auth()->user()->role_id === 2 && $resena->profesional_id === auth()->id()) $puedeReportar = true;
                            $yaReporto = \App\Models\Reporte::where('resena_id', $resena->id)->where('user_id', auth()->id())->exists();
                        @endphp
                        @if($puedeReportar && !$yaReporto)
                        <div x-data="{ abierto: false }" class="mt-2">
                            <button @click="abierto = !abierto"
                                class="flex items-center gap-1 text-gray-300 hover:text-red-400 text-xs cursor-pointer transition">
                                <i class="bi bi-flag"></i> Reportar
                            </button>
                            <div x-show="abierto" x-cloak class="mt-2">
                                <form method="POST" action="{{ route('resenas.reportar', $resena->id) }}">
                                    @csrf
                                    <input type="text" name="motivo" required placeholder="Motivo del reporte..."
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-red-300 bg-gray-50">
                                    <div class="flex justify-end mt-2">
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white text-xs px-4 py-1.5 rounded-lg cursor-pointer transition">
                                            Enviar reporte
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @elseif($yaReporto)
                        <p class="text-xs text-gray-300 mt-2 flex items-center gap-1"><i class="bi bi-flag"></i> Ya reportaste esta reseña.</p>
                        @endif
                        @endauth

                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="bi bi-chat-left-text text-4xl mb-3 block"></i>
                        <p class="text-sm">Aún no hay reseñas para este profesional.</p>
                    </div>
                    @endforelse

                </div>

                {{-- TAB: NEGOCIO --}}
                <div x-show="tab === 'negocio'" x-cloak>
                    @php $negocio = $usuario->perfilProfesional?->negocio; @endphp
                    @if($negocio)
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="bi bi-shop text-blue-500"></i>{{ $negocio->nombre }}
                        </h3>
                        @if($negocio->descripcion)
                        <p class="text-gray-500 text-sm mb-4 leading-relaxed">{{ $negocio->descripcion }}</p>
                        @endif
                        <div class="flex flex-col gap-3 text-sm text-gray-600">
                            @if($negocio->direccion)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                                <i class="bi bi-geo-alt text-blue-500 flex-shrink-0"></i>{{ $negocio->direccion }}
                            </div>
                            @endif
                            @if($negocio->telefono)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                                <i class="bi bi-telephone text-blue-500 flex-shrink-0"></i>{{ $negocio->telefono }}
                            </div>
                            @endif
                            @if($negocio->categoria)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                                <i class="bi bi-tag text-blue-500 flex-shrink-0"></i>{{ ucfirst($negocio->categoria) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-10 text-gray-400">
                        <i class="bi bi-shop text-4xl mb-3 block"></i>
                        <p class="text-sm">Este profesional ofrece servicios a domicilio únicamente.</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- COLUMNA DERECHA — desktop --}}
        <div class="hidden lg:block w-80 flex-shrink-0">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 sticky top-24">

                <h3 class="font-bold text-gray-800 mb-1">
                    Contacta a {{ explode(' ', $usuario->name)[0] }}
                </h3>
                <p class="text-xs text-gray-400 mb-4">Elige fecha y hora — pago seguro en la plataforma</p>

                @php
                    $whatsapp         = $usuario->perfilProfesional->whatsapp ?? null;
                    $primerServicio   = $usuario->servicios->first();
                    $primerServicioId = $primerServicio?->id;
                    $primerServicioTitulo = $primerServicio ? urlencode($primerServicio->titulo) : 'un%20servicio';
                @endphp

                @auth
    @if(auth()->user()->role_id === 1)
    <a href="{{ route('reservas.create', $usuario->id) }}"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center gap-2 mb-3 text-sm font-semibold transition shadow-sm shadow-blue-200">
        <i class="bi bi-calendar-check"></i> Reservar servicio
    </a>
    @endif
@else
<button onclick="openLogin()"
    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center gap-2 mb-3 cursor-pointer text-sm font-semibold">
    <i class="bi bi-calendar-check"></i> Reservar servicio
</button>
@endauth
                {{-- INFO DE CONTACTO --}}
                <div class="flex flex-col gap-2.5 text-sm text-gray-500 pt-4 border-t border-gray-100">
                    @if($usuario->perfilProfesional?->ubicacion)
                    <div class="flex items-center gap-2">
                        <i class="bi bi-geo-alt text-blue-400 flex-shrink-0"></i>
                        <span>{{ $usuario->perfilProfesional->ubicacion }}</span>
                    </div>
                    @endif
                    @if($usuario->perfilProfesional?->experiencia)
                    <div class="flex items-center gap-2">
                        <i class="bi bi-briefcase text-blue-400 flex-shrink-0"></i>
                        <span>{{ $usuario->perfilProfesional->experiencia }}</span>
                    </div>
                    @endif
                </div>

                {{-- ESTADÍSTICAS RÁPIDAS --}}
                @if($completados > 0 || $tasaExito !== null)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Estadísticas</p>
                    <div class="flex flex-col gap-2">
                        @if($completados > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5"><i class="bi bi-check-circle text-green-500"></i> Completados</span>
                            <span class="font-semibold text-gray-800">{{ $completados }}</span>
                        </div>
                        @endif
                        @if($tasaExito !== null)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5"><i class="bi bi-graph-up text-blue-500"></i> Tasa de éxito</span>
                            <span class="font-semibold {{ $tasaExito >= 80 ? 'text-green-600' : 'text-yellow-600' }}">{{ $tasaExito }}%</span>
                        </div>
                        @endif
                        @if($retencion !== null)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5"><i class="bi bi-arrow-repeat text-purple-500"></i> Clientes que vuelven</span>
                            <span class="font-semibold text-purple-600">{{ $retencion }}%</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>

{{-- BOTÓN FLOTANTE MÓVIL --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-100 px-4 py-3 shadow-xl">
    @auth
        @if(auth()->user()->role_id === 1)
        <a href="{{ route('reservas.create', $usuario->id) }}"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center gap-2 font-semibold text-sm">
            <i class="bi bi-calendar-check text-base"></i> Reservar servicio
        </a>
        @endif
    @else
    <button onclick="openLogin()"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center gap-2 font-semibold text-sm cursor-pointer">
        <i class="bi bi-calendar-check text-base"></i> Reservar servicio
    </button>
    @endauth
</div>

@push('scripts')
<script>    
function copiarPerfil() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        document.getElementById('textoCompartir').textContent = '¡Enlace copiado!';
        document.getElementById('iconoCompartir').className = 'bi bi-check-circle-fill text-green-500';
        document.getElementById('btnCompartir').classList.add('border-green-300', 'text-green-600');
        setTimeout(() => {
            document.getElementById('textoCompartir').textContent = 'Compartir perfil';
            document.getElementById('iconoCompartir').className = 'bi bi-share';
            document.getElementById('btnCompartir').classList.remove('border-green-300', 'text-green-600');
        }, 2000);
    });
}
</script>
@endpush

@endsection