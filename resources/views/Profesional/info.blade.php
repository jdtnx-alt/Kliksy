@extends('layouts.app')

@php use App\Helpers\CategoriaHelper; $arbolCats = CategoriaHelper::arbol(); @endphp

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8"
x-data="{
tab: 'perfil',
openModal:false,
modo:'crear',
titulo:'',
descripcion:'',
precio:'',
categoria:'',
subcategoria:'',
duracion:'60',
servicioId:null
}">

@php
    use App\Helpers\OnboardingHelper;
    $progreso        = OnboardingHelper::progreso(auth()->user());
    $activo          = OnboardingHelper::perfilActivo(auth()->user());
    $completados     = \App\Models\Solicitud::where('profesional_id', auth()->id())->where('estado','completada')->count();
    $canceladas      = \App\Models\Solicitud::where('profesional_id', auth()->id())->where('estado','cancelada')->count();
    $totalSols       = $completados + $canceladas;
    $tasaExito       = $totalSols > 0 ? round(($completados / $totalSols) * 100) : null;
    $clientesUnicos  = \App\Models\Solicitud::where('profesional_id', auth()->id())->where('estado','completada')->distinct('cliente_id')->count('cliente_id');
    $clientesRepeat  = \App\Models\Solicitud::where('profesional_id', auth()->id())->where('estado','completada')->select('cliente_id')->groupBy('cliente_id')->havingRaw('COUNT(*) > 1')->count();
    $retencion       = $clientesUnicos > 0 ? round(($clientesRepeat / $clientesUnicos) * 100) : null;
    $totalResenas    = auth()->user()->resenas()->count();
    $promedioRes     = auth()->user()->resenas()->avg('calificacion');
@endphp

{{-- BANNER ONBOARDING --}}
@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 flex items-center gap-2">
    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
    {{ session('error') }}
    <a href="{{ route('verification.send') }}"
        class="ml-auto text-xs font-semibold underline hover:text-red-900 transition"
        onclick="event.preventDefault(); document.getElementById('resend-form').submit();">
        Verificar ahora
    </a>
</div>
<form id="resend-form" method="POST" action="{{ route('verification.send') }}" style="display:none;">
    @csrf
</form>
@endif
@if(!$activo)
<div class="bg-yellow-50 border border-yellow-300 rounded-2xl px-4 py-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap flex-shrink-0 text-center transition">
        Ver progreso ({{ $progreso }}%)
    </a>
</div>
@endif

{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Dashboard Profesional</h1>
        <p class="text-gray-500 text-sm sm:text-base mt-1">Gestiona tu perfil, servicios y reseñas</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap self-start">
        @if($activo)
        <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold flex items-center gap-1.5">
            <i class="bi bi-eye-fill"></i> Visible para clientes
        </span>
        @endif

        {{-- MODO VACACIONES --}}
        @php $enVacaciones = $perfil?->en_vacaciones ?? false; @endphp
        <form method="POST" action="{{ route('profesional.vacaciones') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-full font-semibold transition cursor-pointer
                {{ $enVacaciones
                    ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="bi {{ $enVacaciones ? 'bi-moon-fill' : 'bi-moon' }}"></i>
                {{ $enVacaciones ? 'En vacaciones' : 'Activar vacaciones' }}
            </button>
        </form>
    </div>
</div>

{{-- MÉTRICAS RÁPIDAS --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-gray-800">{{ $completados }}</p>
        <p class="text-xs text-gray-500 mt-1">Completados</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-gray-800">{{ $totalResenas }}</p>
        <p class="text-xs text-gray-500 mt-1">Reseñas</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center">
        @if($tasaExito !== null)
        <p class="text-2xl font-bold {{ $tasaExito >= 80 ? 'text-green-600' : ($tasaExito >= 60 ? 'text-yellow-600' : 'text-red-500') }}">{{ $tasaExito }}%</p>
        @else
        <p class="text-2xl font-bold text-gray-300">—</p>
        @endif
        <p class="text-xs text-gray-500 mt-1">Tasa de éxito</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center">
        @if($retencion !== null)
        <p class="text-2xl font-bold text-blue-600">{{ $retencion }}%</p>
        @else
        <p class="text-2xl font-bold text-gray-300">—</p>
        @endif
        <p class="text-xs text-gray-500 mt-1">Clientes que vuelven</p>
    </div>
</div>

{{-- TABS --}}
<div class="flex gap-2 mb-6">
    <button @click="tab='perfil'"
        :class="tab==='perfil' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600'"
        class="flex-1 flex items-center justify-center gap-2 py-3 border rounded-2xl font-medium cursor-pointer text-sm transition">
        <i class="bi bi-person text-sm"></i>
        <span class="hidden sm:inline">Mi Perfil</span>
    </button>
    <button @click="tab='servicios'"
        :class="tab==='servicios' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600'"
        class="flex-1 flex items-center justify-center gap-2 py-3 border rounded-2xl font-medium cursor-pointer text-sm transition">
        <i class="bi bi-scissors text-sm"></i>
        <span class="hidden sm:inline">Servicios</span>
        <span :class="tab==='servicios' ? 'bg-white/30 text-white' : 'bg-gray-100 text-gray-500'"
            class="text-xs px-1.5 py-0.5 rounded-full">{{ count($servicios) }}</span>
    </button>
    <button @click="tab='resenas'"
        :class="tab==='resenas' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600'"
        class="flex-1 flex items-center justify-center gap-2 py-3 border rounded-2xl font-medium cursor-pointer text-sm transition">
        <i class="bi bi-star text-sm"></i>
        <span class="hidden sm:inline">Reseñas</span>
        @if($totalResenas > 0)
        <span :class="tab==='resenas' ? 'bg-white/30 text-white' : 'bg-yellow-100 text-yellow-600'"
            class="text-xs px-1.5 py-0.5 rounded-full">{{ $totalResenas }}</span>
        @endif
    </button>
    <button @click="tab='negocio'"
        :class="tab==='negocio' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600'"
        class="flex-1 flex items-center justify-center gap-2 py-3 border rounded-2xl font-medium cursor-pointer text-sm transition">
        <i class="bi bi-shop text-sm"></i>
        <span class="hidden sm:inline">Negocio</span>
    </button>
</div>

{{-- TAB: PERFIL --}}
<div x-show="tab === 'perfil'" x-cloak>
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6">

        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-1">Perfil Profesional</h2>
        <p class="text-gray-400 text-sm mb-6">Esta información es visible para todos los clientes</p>

        <form method="POST" action="{{ route('perfil.guardar') }}">
            @csrf

            <div class="mb-5">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Descripción profesional</label>
                <textarea name="descripcion" rows="4"
                    placeholder="Cuéntale a los clientes quién eres, qué haces y por qué deberían elegirte..."
                    class="w-full border border-gray-200 rounded-xl p-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 focus:bg-white transition resize-none"
                >{{ old('descripcion', $perfil->descripcion ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Experiencia</label>
                    <input type="text" name="experiencia"
                        placeholder="Ej: 5 años de experiencia"
                        value="{{ old('experiencia', $perfil->experiencia ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 focus:bg-white transition">
                </div>
                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Ubicación</label>
                    <input type="text" name="ubicacion"
                        placeholder="Ej: Florencia, Caquetá"
                        value="{{ old('ubicacion', $perfil->ubicacion ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 focus:bg-white transition">
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">
                    Número WhatsApp
                    <span class="text-gray-400 font-normal">(código de país sin +, ej: 573001234567)</span>
                </label>
                <div class="relative">
                    <i class="bi bi-whatsapp absolute left-3.5 top-3.5 text-green-500 text-base"></i>
                    <input type="text" name="whatsapp"
                        placeholder="573001234567"
                        value="{{ old('whatsapp', $perfil->whatsapp ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 focus:bg-white transition">
                </div>
            </div>

            <div class="mb-6">
            <label class="block mb-2 text-sm font-semibold text-gray-700">Categorías de servicios</label>
            <p class="text-xs text-gray-400 mb-3">Selecciona las categorías principales en las que ofreces servicios</p>
        
            <div class="flex flex-col gap-3">
                @foreach($arbolCats as $padreSlug => $padre)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    {{-- Padre checkbox --}}
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 transition
                        {{ in_array($padreSlug, old('categorias', $perfil->categorias ?? [])) ? 'bg-blue-50' : 'bg-white' }}">
                        <input type="checkbox" name="categorias[]" value="{{ $padreSlug }}"
                            class="accent-blue-500 w-4 h-4"
                            {{ in_array($padreSlug, old('categorias', $perfil->categorias ?? [])) ? 'checked' : '' }}>
                        <i class="bi {{ $padre['icono'] }} text-blue-500 text-sm"></i>
                        <span class="font-semibold text-sm text-gray-800">{{ $padre['nombre'] }}</span>
                    </label>
                </div>
                @endforeach
            </div>
        </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl cursor-pointer text-sm font-semibold transition shadow-sm shadow-blue-200">
                            <i class="bi bi-check-lg mr-1.5"></i>Guardar Perfil
                        </button>
                    </div>
                </form>

        {{-- VERIFICACIÓN DE IDENTIDAD --}}
<div class="mt-8 pt-6 border-t border-gray-100">
    <div class="flex items-center justify-between mb-2">
        <div>
            <h3 class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                <i class="bi bi-shield-check text-blue-500"></i>
                Verificación de identidad
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">
                Sube tu cédula para generar más confianza con los clientes
            </p>
        </div>
        @if($perfil?->cedula_frontal && $perfil?->cedula_trasera)
        <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold flex items-center gap-1.5 flex-shrink-0">
            <i class="bi bi-patch-check-fill"></i> Verificado
        </span>
        @elseif($perfil?->cedula_frontal || $perfil?->cedula_trasera)
        <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-semibold flex items-center gap-1.5 flex-shrink-0">
            <i class="bi bi-hourglass-split"></i> Incompleto
        </span>
        @else
        <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1.5 rounded-full font-medium flex-shrink-0">
            Sin verificar
        </span>
        @endif
    </div>

    <form method="POST" action="{{ route('profesional.cedula') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">

            {{-- FRONTAL --}}
            <div>
                <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                    <i class="bi bi-front text-blue-400"></i> Parte delantera
                </p>

                @if($perfil?->cedula_frontal)
                <div class="relative mb-2 rounded-xl overflow-hidden border border-green-200 bg-green-50">
                    <div class="flex items-center gap-2 px-3 py-2.5">
                        <i class="bi bi-check-circle-fill text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Foto cargada</span>
                    </div>
                </div>
                @endif

                {{-- DESKTOP: input file --}}
                <div class="sm:block hidden">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
                        onclick="document.getElementById('cedula_frontal_desktop').click()">
                        <i class="bi bi-cloud-upload text-2xl text-gray-300 mb-1 block"></i>
                        <p class="text-xs text-gray-500 font-medium">Cargar foto</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 5MB</p>
                        <input type="file" id="cedula_frontal_desktop" name="cedula_frontal"
                            accept="image/*" class="hidden"
                            onchange="previewCedula(this, 'preview_frontal')">
                    </div>
                    <div id="preview_frontal" class="hidden mt-2">
                        <img class="w-full h-24 object-cover rounded-xl border border-gray-200">
                    </div>
                </div>

                {{-- MÓVIL: botón cámara (no funcional por ahora) --}}
                <div class="sm:hidden">
                    <button type="button"
                        onclick="alert('Función disponible próximamente')"
                        class="w-full border-2 border-dashed border-gray-200 rounded-xl p-4 flex flex-col items-center gap-1.5 text-gray-400 hover:border-blue-400 hover:text-blue-500 transition cursor-pointer bg-white">
                        <i class="bi bi-camera text-2xl"></i>
                        <span class="text-xs font-medium">Tomar foto (próximamente)</span>
                    </button>
                </div>
            </div>

            {{-- TRASERA --}}
            <div>
                <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                    <i class="bi bi-back text-blue-400"></i> Parte trasera
                </p>

                @if($perfil?->cedula_trasera)
                <div class="relative mb-2 rounded-xl overflow-hidden border border-green-200 bg-green-50">
                    <div class="flex items-center gap-2 px-3 py-2.5">
                        <i class="bi bi-check-circle-fill text-green-500"></i>
                        <span class="text-xs text-green-700 font-medium">Foto cargada</span>
                    </div>
                </div>
                @endif

                {{-- DESKTOP --}}
                <div class="sm:block hidden">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
                        onclick="document.getElementById('cedula_trasera_desktop').click()">
                        <i class="bi bi-cloud-upload text-2xl text-gray-300 mb-1 block"></i>
                        <p class="text-xs text-gray-500 font-medium">Cargar foto</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 5MB</p>
                        <input type="file" id="cedula_trasera_desktop" name="cedula_trasera"
                            accept="image/*" class="hidden"
                            onchange="previewCedula(this, 'preview_trasera')">
                    </div>
                    <div id="preview_trasera" class="hidden mt-2">
                        <img class="w-full h-24 object-cover rounded-xl border border-gray-200">
                    </div>
                </div>

                {{-- MÓVIL --}}
                <div class="sm:hidden">
                    <button type="button"
                        onclick="alert('Función disponible próximamente')"
                        class="w-full border-2 border-dashed border-gray-200 rounded-xl p-4 flex flex-col items-center gap-1.5 text-gray-400 hover:border-blue-400 hover:text-blue-500 transition cursor-pointer bg-white">
                        <i class="bi bi-camera text-2xl"></i>
                        <span class="text-xs font-medium">Tomar foto (próximamente)</span>
                    </button>
                </div>
            </div>

        </div>

        {{-- BOTÓN GUARDAR --}}
        <div class="flex justify-end mt-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm shadow-blue-200 flex items-center gap-2 cursor-pointer">
                <i class="bi bi-shield-check"></i> Guardar cédula
            </button>
        </div>

    </form>

    {{-- NOTA PRIVACIDAD --}}
    <p class="text-xs text-gray-400 mt-3 flex items-center gap-1.5">
        <i class="bi bi-lock text-gray-300"></i>
        Tus documentos se almacenan de forma privada y segura. Solo tú puedes verlos.
    </p>
</div>

    </div>
</div>

{{-- TAB: SERVICIOS --}}
<div x-show="tab === 'servicios'" x-cloak>
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-800">Mis servicios</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ count($servicios) }} servicios publicados</p>
            </div>
            <button @click="openModal = true; modo = 'crear'; titulo = ''; descripcion = ''; precio = ''; categoria = '';"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl cursor-pointer text-sm font-semibold transition shadow-sm shadow-blue-200 flex items-center gap-1.5">
                <i class="bi bi-plus-lg"></i> Nuevo servicio
            </button>
        </div>

        @if(count($servicios) === 0)
        <div class="text-center py-12 text-gray-400">
            <i class="bi bi-scissors text-5xl mb-4 block"></i>
            <p class="font-medium text-gray-500">Aún no tienes servicios</p>
            <p class="text-sm mt-1">Publica tu primer servicio para que los clientes puedan contactarte</p>
            <button @click="openModal = true; modo = 'crear'; titulo = ''; descripcion = ''; precio = ''; categoria = '';"
                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer">
                + Publicar primer servicio
            </button>
        </div>
        @else
        <div class="flex flex-col gap-3">
            @foreach($servicios as $servicio)
<div class="border border-gray-200 rounded-2xl overflow-hidden hover:border-blue-200 hover:shadow-sm transition">
    {{-- FOTO --}}
    @if($servicio->foto)
    <img src="{{ asset('storage/' . $servicio->foto) }}" class="w-full h-36 object-cover">
    @else
    <div class="w-full h-36 bg-gray-50 flex items-center justify-center border-b border-gray-100">
        <i class="bi bi-image text-3xl text-gray-200"></i>
    </div>
    @endif
    <div class="p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full">{{ ucfirst($servicio->categoria) }}</span>
                </div>
                <h3 class="font-bold text-gray-800 text-sm sm:text-base">{{ $servicio->titulo }}</h3>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">{{ $servicio->descripcion }}</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-4 flex-shrink-0">
                <div class="text-right">
                    <span class="text-blue-600 font-bold text-lg block">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-400"><i class="bi bi-clock"></i> {{ $servicio->duracion ?? 60 }} min</span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openModal = true; modo = 'editar'; servicioId = {{ $servicio->id }}; titulo = '{{ addslashes($servicio->titulo) }}'; descripcion = '{{ addslashes($servicio->descripcion) }}'; precio = '{{ $servicio->precio }}'; categoria = '{{ $servicio->categoria }}'; duracion = '{{ $servicio->duracion ?? 60 }}';"
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer">
                        <i class="bi bi-pencil text-sm"></i>
                    </button>
                    <form action="{{ route('profesional.servicios.destroy', $servicio->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Eliminar este servicio?')"
                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer">
                            <i class="bi bi-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
        </div>
        @endif

    </div>
</div>

{{-- MODAL SERVICIO --}}
<div x-show="openModal"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4"
    style="display:none">
    <div @click.away="openModal=false"
        class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">

        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-800" x-text="modo === 'crear' ? 'Nuevo servicio' : 'Editar servicio'"></h2>
                <p class="text-xs text-gray-400 mt-0.5">Los clientes verán esta información en tu perfil</p>
            </div>
            <button @click="openModal=false"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition cursor-pointer">
                ✕
            </button>
        </div>

        <form method="POST" enctype="multipart/form-data"
    :action="modo === 'crear' ? '{{ route('profesional.servicios.store') }}' : '/profesional/servicios/' + servicioId">
            @csrf
            <template x-if="modo === 'editar'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="mb-4">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Título del servicio</label>
                <input type="text" name="titulo" x-model="titulo"
                    placeholder="Ej: Corte de cabello a domicilio"
                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div class="mb-4">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Descripción</label>
                <textarea name="descripcion" rows="3" x-model="descripcion"
                    placeholder="Describe qué incluye el servicio..."
                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"></textarea>
            </div>
            {{-- CATEGORÍA PADRE --}}
            <div class="mb-4">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Categoría</label>
                <select name="categoria" x-model="categoria"
                    @change="subcategoria = ''"
                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <option value="">Seleccionar categoría</option>
                    @foreach($arbolCats as $slug => $cat)
                    <option value="{{ $slug }}">{{ $cat['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- SUBCATEGORÍA (aparece dinámicamente según padre) --}}
            <div class="mb-4" x-show="categoria !== ''" x-cloak>
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Subcategoría <span class="text-gray-400 font-normal">(opcional)</span></label>
                <select name="subcategoria" x-model="subcategoria"
                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <option value="">Todas las subcategorías</option>
                    @foreach($arbolCats as $slug => $cat)
                    <template x-if="categoria === '{{ $slug }}'">
                        <template x-for="false"><!-- forzar render --></template>
                    </template>
                    @foreach($cat['subs'] as $subSlug => $subNombre)
                    <option value="{{ $subSlug }}"
                        x-show="categoria === '{{ $slug }}'">{{ $subNombre }}</option>
                    @endforeach
                    @endforeach
                </select>
            </div>
            <div class="mb-6">
                <label class="block mb-1.5 text-sm font-semibold text-gray-700">Precio (COP)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-gray-400 text-sm">$</span>
                    <input type="number" name="precio" x-model="precio"
                        placeholder="0"
                        class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
            </div>
            <div class="mb-6">
    <label class="block mb-1.5 text-sm font-semibold text-gray-700">
        Duración estimada
        <span class="text-gray-400 font-normal">(minutos)</span>
    </label>
    <select name="duracion" x-model="duracion"
        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        <option value="15">15 minutos</option>
        <option value="30">30 minutos</option>
        <option value="45">45 minutos</option>
        <option value="60" selected>1 hora</option>
        <option value="90">1 hora 30 min</option>
        <option value="120">2 horas</option>
        <option value="180">3 horas</option>
        <option value="240">4 horas</option>
    </select>
    {{-- FOTO DEL SERVICIO --}}
<div class="mb-6">
    <label class="block mb-1.5 text-sm font-semibold text-gray-700">
        Foto del servicio
        <span class="text-gray-400 font-normal">(opcional)</span>
    </label>
    <div x-data="{ preview: null }">
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
            onclick="document.getElementById('modalFotoServicio').click()">
            <template x-if="!preview">
                <div>
                    <i class="bi bi-image text-2xl text-gray-300 mb-1 block"></i>
                    <p class="text-xs text-gray-500 font-medium">Haz clic para subir una foto</p>
                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 2MB</p>
                </div>
            </template>
            <template x-if="preview">
                <img :src="preview" class="w-full h-32 object-cover rounded-xl">
            </template>
            <input type="file" id="modalFotoServicio" name="foto"
                accept="image/*" class="hidden"
                @change="preview = URL.createObjectURL($event.target.files[0])">
        </div>
    </div>
</div>
</div>
            <div class="flex gap-3">
                <button type="button" @click="openModal=false"
                    class="flex-1 border border-gray-200 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TAB: RESEÑAS --}}
<div x-show="tab === 'resenas'" x-cloak>
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6">

        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-1">Mis Reseñas</h2>
        <p class="text-gray-400 text-sm mb-6">Lo que dicen tus clientes sobre ti</p>

        @php
            $resenas        = auth()->user()->resenas()->with('cliente')->latest()->get();
            $promedioPerfil = $resenas->count() ? round($resenas->avg('calificacion'), 1) : 0;
        @endphp

        {{-- RESUMEN --}}
        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row gap-5 sm:gap-8 items-center">
                <div class="text-center flex-shrink-0">
                    <p class="text-5xl sm:text-6xl font-bold text-gray-800">{{ $promedioPerfil }}</p>
                    <div class="flex gap-1 justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $promedioPerfil ? 'text-yellow-400' : 'text-gray-200' }} text-xl">★</span>
                        @endfor
                    </div>
                    <p class="text-gray-400 text-xs mt-1.5">{{ $resenas->count() }} reseñas verificadas</p>
                </div>
                <div class="flex-1 w-full">
                    @foreach([5,4,3,2,1] as $estrella)
                    @php
                        $total      = $resenas->count();
                        $cantidad   = $resenas->where('calificacion', $estrella)->count();
                        $porcentaje = $total ? ($cantidad / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs text-gray-500 w-2 flex-shrink-0">{{ $estrella }}</span>
                        <span class="text-yellow-400 text-xs flex-shrink-0">★</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-yellow-400 h-2 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 w-4 text-right flex-shrink-0">{{ $cantidad }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- LISTA RESEÑAS --}}
        @forelse($resenas as $resena)
        <div class="border border-gray-200 rounded-2xl p-4 sm:p-5 mb-3">

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
                <div class="flex gap-0.5 self-start sm:self-auto">
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
                    <p class="font-semibold text-gray-800 text-xs sm:text-sm">{{ $respuesta->user->name }}</p>
                    @if($respuesta->user_id === auth()->id())
                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Profesional</span>
                    @endif
                    <p class="text-xs text-gray-400">{{ $respuesta->created_at->diffForHumans() }}</p>
                </div>
                <p class="text-gray-600 text-sm">{{ $respuesta->contenido }}</p>
            </div>
            @endforeach

            @auth
            @if(auth()->id() == $resena->profesional_id || auth()->id() == $resena->cliente_id)
            <div x-data="{ abierto: false }" class="mt-3">
                <button @click="abierto = !abierto"
                    class="flex items-center gap-1.5 text-gray-400 hover:text-blue-500 text-xs cursor-pointer transition">
                    <i class="bi bi-chat"></i> Responder
                </button>
                <div x-show="abierto" x-cloak class="mt-3">
                    <form method="POST" action="{{ route('resenas.responder', $resena->id) }}">
                        @csrf
                        <textarea name="contenido" rows="2"
                            placeholder="Escribe tu respuesta..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50"></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-2 rounded-lg cursor-pointer transition">
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @endauth

            @php
                $yaReporto = \App\Models\Reporte::where('resena_id', $resena->id)->where('user_id', auth()->id())->exists();
            @endphp
            @if(!$yaReporto)
            <div x-data="{ abierto: false }" class="mt-2">
                <button @click="abierto = !abierto"
                    class="flex items-center gap-1 text-gray-300 hover:text-red-400 text-xs cursor-pointer transition">
                    <i class="bi bi-flag"></i> Reportar
                </button>
                <div x-show="abierto" x-cloak class="mt-2">
                    <form method="POST" action="{{ route('resenas.reportar', $resena->id) }}">
                        @csrf
                        <input type="text" name="motivo" required
                            placeholder="Motivo del reporte..."
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
            @else
            <p class="text-xs text-gray-300 mt-2 flex items-center gap-1"><i class="bi bi-flag"></i> Ya reportaste esta reseña.</p>
            @endif

        </div>
        @empty
        <div class="text-center py-12 text-gray-400">
            <i class="bi bi-chat-left-text text-5xl mb-4 block"></i>
            <p class="font-medium text-gray-500">Aún no tienes reseñas</p>
            <p class="text-sm mt-1">Cuando completes servicios, tus clientes podrán dejarte reseñas</p>
        </div>
        @endforelse

    </div>
</div>

{{-- TAB: NEGOCIO --}}
<div x-show="tab === 'negocio'" x-cloak>
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6"
        x-data="{ tieneNegocio: {{ $negocio ? 'true' : 'false' }} }">

        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-1">Mi Negocio Físico</h2>
        <p class="text-gray-400 text-sm mb-6">Registra tu negocio para que los clientes puedan visitarte</p>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif

        <div class="flex items-center gap-3 mb-6 p-4 bg-gray-50 rounded-xl">
            <button type="button"
                @click="tieneNegocio = !tieneNegocio"
                :class="tieneNegocio ? 'bg-blue-500' : 'bg-gray-300'"
                class="relative w-12 h-6 rounded-full transition-colors duration-200 cursor-pointer flex-shrink-0">
                <span
                    :class="tieneNegocio ? 'translate-x-6' : 'translate-x-1'"
                    class="absolute top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm block">
                </span>
            </button>
            <div>
                <span class="font-semibold text-gray-700 text-sm sm:text-base">Tengo negocio físico</span>
                <p class="text-xs text-gray-400 mt-0.5">Los clientes podrán ver tu dirección y visitarte</p>
            </div>
        </div>

        <div x-show="tieneNegocio" x-cloak>
            <form method="POST" action="{{ route('negocio.guardar') }}">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Nombre del negocio</label>
                    <input type="text" name="nombre"
                        placeholder="Ej: Barbería El Estilo"
                        value="{{ old('nombre', $negocio->nombre ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="3"
                        placeholder="Cuéntale a los clientes sobre tu negocio..."
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"
                    >{{ old('descripcion', $negocio->descripcion ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Dirección</label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-3.5 top-3.5 text-blue-400 text-sm"></i>
                        <input type="text" name="direccion"
                            placeholder="Ej: Calle 10 #5-23, Florencia"
                            value="{{ old('direccion', $negocio->direccion ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Teléfono</label>
                        <input type="text" name="telefono"
                            placeholder="Ej: 3001234567"
                            value="{{ old('telefono', $negocio->telefono ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Categoría principal</label>
                        <select name="categoria"
                            class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                            <option value="">Seleccionar</option>
                            @foreach($categorias as $valor => $cat)
                            <option value="{{ $valor }}"
                                {{ old('categoria', $negocio->categoria ?? '') === $valor ? 'selected' : '' }}>
                                {{ $cat['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl cursor-pointer flex items-center gap-2 text-sm font-semibold transition shadow-sm shadow-blue-200">
                        <i class="bi bi-check-lg"></i> Guardar Negocio
                    </button>
                </div>
            </form>
        </div>

        <div x-show="!tieneNegocio" x-cloak class="text-center py-10 text-gray-400">
            <i class="bi bi-shop text-5xl mb-3 block"></i>
            <p class="font-medium text-gray-500">Sin negocio físico registrado</p>
            <p class="text-sm mt-1">Activa el toggle si tienes un local o negocio físico</p>
        </div>

    </div>
</div>

</div>
@push('scripts')
<script>
function previewCedula(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.classList.remove('hidden');
            preview.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection