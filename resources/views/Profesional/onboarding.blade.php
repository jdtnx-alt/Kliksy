@extends('layouts.app')

@section('content')

@php
    use App\Helpers\OnboardingHelper;
    $pasos       = OnboardingHelper::pasos(auth()->user());
    $progreso    = OnboardingHelper::progreso(auth()->user());
    $activo      = OnboardingHelper::perfilActivo(auth()->user());
    $completados = collect($pasos)->where('completado', true)->count();
    $total       = count($pasos);
    $perfil      = auth()->user()->perfilProfesional;

    // Índice del primer paso incompleto
    $primerIncompleto = collect($pasos)->search(fn($p) => !$p['completado']);
    $primerIncompleto = $primerIncompleto === false ? $total : $primerIncompleto;
@endphp

<div class="max-w-xl mx-auto py-8 sm:py-12 px-4"
     x-data="{
        pasoAbierto: '{{ $pasos[$primerIncompleto < $total ? $primerIncompleto : 0]['id'] ?? '' }}',
        intentoBloqueo: false,
        mensajeBloqueo: ''
     }">

    {{-- HEADER --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-2xl mx-auto mb-5 shadow-lg shadow-blue-200">K</div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
            ¡Bienvenido, {{ explode(' ', auth()->user()->name)[0] }}!
        </h1>
        <p class="text-gray-500 text-sm sm:text-base">
            Completa cada paso en orden para activar tu perfil
        </p>
    </div>

    {{-- MENSAJE BLOQUEO --}}
    <div x-show="intentoBloqueo"
         x-transition
         x-init="$watch('intentoBloqueo', v => { if(v) setTimeout(() => intentoBloqueo = false, 3000) })"
         class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-2xl mb-5 text-sm flex items-center gap-2">
        <i class="bi bi-lock-fill text-yellow-500 flex-shrink-0"></i>
        <span x-text="mensajeBloqueo"></span>
    </div>

    {{-- BARRA DE PROGRESO --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="font-bold text-gray-800">Perfil al <span class="text-blue-600">{{ $progreso }}%</span></p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $completados }} de {{ $total }} pasos completados</p>
            </div>
            @if($activo)
            <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold flex items-center gap-1.5">
                <i class="bi bi-eye-fill"></i> Visible para clientes
            </span>
            @else
            <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-semibold flex items-center gap-1.5">
                <i class="bi bi-eye-slash-fill"></i> No visible aún
            </span>
            @endif
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-1000"
                style="width: {{ $progreso }}%; background: linear-gradient(90deg, #2563eb, #16a34a);">
            </div>
        </div>
        @if(!$activo)
        <p class="text-xs text-gray-400 mt-2.5 flex items-center gap-1">
            <i class="bi bi-lightning-fill text-yellow-500"></i>
            Completa los pasos en orden para aparecer en búsquedas
        </p>
        @else
        <p class="text-xs text-green-600 mt-2.5 flex items-center gap-1">
            <i class="bi bi-check-circle-fill"></i>
            Tu perfil ya aparece en búsquedas.
        </p>
        @endif
    </div>

    {{-- PASOS ACORDEÓN --}}
    <div class="flex flex-col gap-3 mb-6">

        @foreach($pasos as $index => $paso)
        @php
            // Puede abrir este paso si todos los anteriores están completos
            $puedeAbrir = $index === 0 || collect($pasos)->slice(0, $index)->every(fn($p) => $p['completado']);
            $anteriorTitulo = $index > 0 ? $pasos[$index - 1]['titulo'] : '';
        @endphp

        <div class="bg-white border rounded-2xl overflow-hidden shadow-sm transition-all duration-200
            {{ $paso['completado'] ? 'border-green-200' : ($puedeAbrir ? 'border-gray-200' : 'border-gray-100 opacity-60') }}">

            {{-- CABECERA --}}
            <button type="button"
                @click="
                    @if($puedeAbrir)
                        pasoAbierto = pasoAbierto === '{{ $paso['id'] }}' ? '' : '{{ $paso['id'] }}'
                    @else
                        intentoBloqueo = true;
                        mensajeBloqueo = 'Primero debes completar: {{ addslashes($anteriorTitulo) }}'
                    @endif
                "
                class="w-full flex items-center gap-4 p-4 sm:p-5 text-left transition
                    {{ $puedeAbrir ? 'cursor-pointer hover:bg-gray-50' : 'cursor-not-allowed' }}">

                <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-sm font-bold
                    {{ $paso['completado'] ? 'bg-green-500 text-white' : ($puedeAbrir ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400') }}">
                    @if($paso['completado'])
                        <i class="bi bi-check-lg text-base"></i>
                    @elseif(!$puedeAbrir)
                        <i class="bi bi-lock text-sm"></i>
                    @else
                        <i class="bi {{ $paso['icono'] }} text-base"></i>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-sm sm:text-base
                            {{ $paso['completado'] ? 'line-through text-gray-400' : ($puedeAbrir ? 'text-gray-800' : 'text-gray-400') }}">
                            {{ $paso['titulo'] }}
                        </p>
                        @if($paso['requerido'] && !$paso['completado'])
                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-medium">⚡ Requerido</span>
                        @elseif(!$paso['requerido'] && !$paso['completado'])
                        <span class="bg-gray-100 text-gray-400 text-xs px-2 py-0.5 rounded-full">Opcional</span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm mt-0.5 {{ $puedeAbrir ? 'text-gray-400' : 'text-gray-300' }}">
                        {{ $paso['descripcion'] }}
                    </p>
                </div>

                <div class="flex-shrink-0">
                    @if($paso['completado'])
                        <i class="bi bi-check-circle-fill text-green-500 text-lg"></i>
                    @elseif(!$puedeAbrir)
                        <i class="bi bi-lock text-gray-300 text-base"></i>
                    @else
                        <i class="bi bi-chevron-down text-gray-300 text-base transition-transform duration-200"
                           :class="pasoAbierto === '{{ $paso['id'] }}' ? 'rotate-180' : ''"></i>
                    @endif
                </div>
            </button>

            {{-- CONTENIDO --}}
            @if($puedeAbrir)
            <div x-show="pasoAbierto === '{{ $paso['id'] }}'"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="border-t border-gray-100 px-4 sm:px-5 py-5">

                {{-- Mensajes de éxito solo en el paso correcto --}}
                @if(session('success') && session('paso_completado') === $paso['id'])
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
                @endif

                {{-- ── CÉDULA ── --}}
                @if($paso['id'] === 'cedula')
                <form method="POST" action="{{ route('profesional.cedula') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                                <i class="bi bi-front text-blue-400"></i> Parte delantera
                            </p>
                            @if($perfil?->cedula_frontal)
                            <div class="flex items-center gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-xl mb-2">
                                <i class="bi bi-check-circle-fill text-green-500"></i>
                                <span class="text-xs text-green-700 font-medium">Foto cargada</span>
                            </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
                                onclick="document.getElementById('ob_cedula_frontal').click()">
                                <i class="bi bi-cloud-upload text-2xl text-gray-300 mb-1 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Cargar foto delantera</p>
                                <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 5MB</p>
                                <input type="file" id="ob_cedula_frontal" name="cedula_frontal"
                                    accept="image/*" class="hidden"
                                    onchange="previewOnboarding(this, 'ob_preview_frontal')">
                            </div>
                            <div id="ob_preview_frontal" class="hidden mt-2">
                                <img class="w-full h-24 object-cover rounded-xl border border-gray-200">
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                                <i class="bi bi-back text-blue-400"></i> Parte trasera
                            </p>
                            @if($perfil?->cedula_trasera)
                            <div class="flex items-center gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-xl mb-2">
                                <i class="bi bi-check-circle-fill text-green-500"></i>
                                <span class="text-xs text-green-700 font-medium">Foto cargada</span>
                            </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
                                onclick="document.getElementById('ob_cedula_trasera').click()">
                                <i class="bi bi-cloud-upload text-2xl text-gray-300 mb-1 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Cargar foto trasera</p>
                                <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 5MB</p>
                                <input type="file" id="ob_cedula_trasera" name="cedula_trasera"
                                    accept="image/*" class="hidden"
                                    onchange="previewOnboarding(this, 'ob_preview_trasera')">
                            </div>
                            <div id="ob_preview_trasera" class="hidden mt-2">
                                <img class="w-full h-24 object-cover rounded-xl border border-gray-200">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-4 flex items-center gap-1.5">
                        <i class="bi bi-lock text-gray-300"></i>
                        Tus documentos se almacenan de forma privada y segura
                    </p>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition cursor-pointer flex items-center justify-center gap-2">
                        <i class="bi bi-shield-check"></i> Guardar cédula y continuar
                    </button>
                </form>

                {{-- ── PERFIL ── --}}
                @elseif($paso['id'] === 'perfil')
                <form method="POST" action="{{ route('perfil.guardar') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción profesional</label>
                        <textarea name="descripcion" rows="3"
                            placeholder="Cuéntale a los clientes quién eres y qué haces..."
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 focus:bg-white transition resize-none"
                        >{{ old('descripcion', $perfil->descripcion ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Experiencia</label>
                            <input type="text" name="experiencia"
                                placeholder="Ej: 5 años de experiencia"
                                value="{{ old('experiencia', $perfil->experiencia ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ubicación</label>
                            <input type="text" name="ubicacion"
                                placeholder="Ej: Florencia, Caquetá"
                                value="{{ old('ubicacion', $perfil->ubicacion ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            WhatsApp <span class="text-gray-400 font-normal">(código de país sin +)</span>
                        </label>
                        <div class="relative">
                            <i class="bi bi-whatsapp absolute left-3 top-3 text-green-500"></i>
                            <input type="text" name="whatsapp"
                                placeholder="573001234567"
                                value="{{ old('whatsapp', $perfil->whatsapp ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Duración Promedio del Servicio</label>
                        <p class="text-[10px] text-gray-400 mb-2 leading-tight">Incluye el tiempo de transporte. Ej: Si seleccionas 50 min y empiezas a las 7:00, las reservas serán 7:00, 7:50, etc.</p>
                        <select name="duracion_promedio"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                            @php $oldDuracion = old('duracion_promedio', $perfil->duracion_promedio ?? 60); @endphp
                            <option value="15" {{ $oldDuracion == 15 ? 'selected' : '' }}>15 minutos</option>
                            <option value="30" {{ $oldDuracion == 30 ? 'selected' : '' }}>30 minutos</option>
                            <option value="45" {{ $oldDuracion == 45 ? 'selected' : '' }}>45 minutos</option>
                            <option value="60" {{ $oldDuracion == 60 ? 'selected' : '' }}>1 hora</option>
                            <option value="75" {{ $oldDuracion == 75 ? 'selected' : '' }}>1 hora y 15 minutos</option>
                            <option value="90" {{ $oldDuracion == 90 ? 'selected' : '' }}>1 hora y 30 minutos</option>
                            <option value="105" {{ $oldDuracion == 105 ? 'selected' : '' }}>1 hora y 45 minutos</option>
                            <option value="120" {{ $oldDuracion == 120 ? 'selected' : '' }}>2 horas</option>
                            <option value="150" {{ $oldDuracion == 150 ? 'selected' : '' }}>2 horas y 30 minutos</option>
                            <option value="180" {{ $oldDuracion == 180 ? 'selected' : '' }}>3 horas</option>
                            <option value="240" {{ $oldDuracion == 240 ? 'selected' : '' }}>4 horas (Medio día)</option>
                            <option value="0" {{ $oldDuracion == 0 ? 'selected' : '' }}>Todo el día</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Días de Trabajo</label>
                        <p class="text-[10px] text-gray-400 mb-2">Selecciona los días que estás disponible. Los días desmarcados no podrán ser reservados.</p>
                        @php
                            $dias = [
                                'lun' => 'Lunes', 'mar' => 'Martes', 'mie' => 'Miércoles',
                                'jue' => 'Jueves', 'vie' => 'Viernes', 'sab' => 'Sábado', 'dom' => 'Domingo'
                            ];
                            $laborablesGuardados = old('dias_laborables', $perfil->dias_laborables ?? ['lun','mar','mie','jue','vie']);
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($dias as $codigo => $nombre)
                            <label class="cursor-pointer border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium hover:bg-blue-50 transition select-none flex items-center gap-2 has-[:checked]:bg-blue-100 has-[:checked]:border-blue-600 has-[:checked]:text-blue-800">
                                <input type="checkbox" name="dias_laborables[]" value="{{ $codigo }}" class="accent-blue-600 w-3 h-3"
                                    {{ in_array($codigo, $laborablesGuardados) ? 'checked' : '' }}>
                                {{ $nombre }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition cursor-pointer flex items-center justify-center gap-2">
                        <i class="bi bi-check-lg"></i> Guardar perfil y continuar
                    </button>
                </form>

                {{-- ── SERVICIO ── --}}
@elseif($paso['id'] === 'servicio')
@php $arbolCats = \App\Helpers\CategoriaHelper::arbol(); @endphp
<form method="POST" action="{{ route('profesional.servicios.store') }}"
    x-data="{ catSeleccionada: '' }">
    @csrf
    <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Título del servicio</label>
        <input type="text" name="titulo"
            placeholder="Ej: Corte de cabello a domicilio"
            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
    </div>
    <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción</label>
        <textarea name="descripcion" rows="2"
            placeholder="Describe qué incluye el servicio..."
            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"></textarea>
    </div>

    {{-- CATEGORÍA PADRE --}}
    <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Categoría</label>
        <select name="categoria"
            x-model="catSeleccionada"
            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <option value="">Seleccionar categoría</option>
            @foreach($arbolCats as $slug => $cat)
            <option value="{{ $slug }}">{{ $cat['nombre'] }}</option>
            @endforeach
        </select>
    </div>

    {{-- SUBCATEGORÍA — aparece solo cuando hay categoría seleccionada --}}
    @foreach($arbolCats as $slug => $cat)
    <div class="mb-4" x-show="catSeleccionada === '{{ $slug }}'" x-cloak>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Subcategoría <span class="text-gray-400 font-normal">(opcional)</span>
        </label>
        <select name="subcategoria"
            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <option value="">Todas las subcategorías</option>
            @foreach($cat['subs'] as $subSlug => $subNombre)
            <option value="{{ $subSlug }}">{{ $subNombre }}</option>
            @endforeach
        </select>
    </div>
    @endforeach

    <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Precio (COP)</label>
        <div class="relative">
            <span class="absolute left-3 top-3 text-gray-400 text-sm">$</span>
            <input type="number" name="precio" placeholder="0"
                class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </div>
    </div>
    <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition cursor-pointer flex items-center justify-center gap-2">
        <i class="bi bi-plus-lg"></i> Publicar servicio y continuar
    </button>
</form>
                {{-- ── FOTOS ── --}}
                @elseif($paso['id'] === 'foto')
                @php $fotosActuales = auth()->user()->perfilProfesional?->fotos ?? collect(); @endphp
                <form method="POST" action="{{ route('perfil.fotos.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($fotosActuales->count())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($fotosActuales as $foto)
                        <img src="{{ asset('storage/' . $foto->ruta) }}"
                            class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                        @endforeach
                    </div>
                    @endif
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer"
                        onclick="document.getElementById('ob_fotos').click()">
                        <i class="bi bi-cloud-upload text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-sm text-gray-500 font-medium">Haz clic para subir fotos</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 2MB por foto</p>
                        <input type="file" name="fotos[]" multiple accept="image/*"
                            class="hidden" id="ob_fotos" onchange="this.form.submit()">
                    </div>
                    @if($fotosActuales->count() > 0)
                    <div class="mt-4">
                        <a href="{{ route('profesional.dashboard') }}"
                            class="w-full flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl text-sm font-semibold transition">
                            <i class="bi bi-rocket-takeoff"></i> ¡Listo! Ir a mi dashboard
                        </a>
                    </div>
                    @endif
                </form>

                {{-- ── NEGOCIO ── --}}
                @elseif($paso['id'] === 'negocio')
                <form method="POST" action="{{ route('negocio.guardar') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre del negocio</label>
                        <input type="text" name="nombre"
                            placeholder="Ej: Barbería El Estilo"
                            value="{{ old('nombre', $perfil?->negocio?->nombre ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dirección</label>
                        <input type="text" name="direccion"
                            placeholder="Ej: Calle 10 #5-23, Florencia"
                            value="{{ old('direccion', $perfil?->negocio?->direccion ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
                            <input type="text" name="telefono"
                                placeholder="3001234567"
                                value="{{ old('telefono', $perfil?->negocio?->telefono ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Categoría</label>
                            <select name="categoria"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                                <option value="">Seleccionar</option>
                                @foreach(\App\Helpers\CategoriaHelper::arbol() as $slug => $cat)
                                <option value="{{ $slug }}">{{ $cat['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('profesional.dashboard') }}"
                            class="flex-1 border border-gray-200 text-gray-500 hover:bg-gray-50 py-3 rounded-xl text-sm font-medium text-center transition">
                            Omitir por ahora
                        </a>
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition cursor-pointer flex items-center justify-center gap-2">
                            <i class="bi bi-check-lg"></i> Guardar negocio
                        </button>
                    </div>
                </form>
                @endif

            </div>
            @endif

        </div>
        @endforeach

    </div>

    {{-- BOTÓN FINAL --}}
    @if($activo)
    <a href="{{ route('profesional.dashboard') }}"
        class="block w-full bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl font-bold text-center transition shadow-lg shadow-green-200 text-sm sm:text-base mb-4">
        <i class="bi bi-rocket-takeoff mr-2"></i> ¡Listo! Ir a mi dashboard
    </a>
    @endif

    {{-- MENSAJE MOTIVACIONAL --}}
    @if(!$activo)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 text-center">
        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="bi bi-graph-up-arrow text-blue-600 text-lg"></i>
        </div>
        <p class="font-semibold text-gray-800 mb-1">¿Sabías que...</p>
        <p class="text-sm text-gray-600">Los profesionales con perfil completo reciben <strong class="text-blue-600">3 veces más solicitudes</strong> que los que no lo tienen.</p>
    </div>
    @else
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-5 text-center">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="bi bi-stars text-green-600 text-lg"></i>
        </div>
        <p class="font-semibold text-gray-800 mb-1">¡Tu perfil está activo!</p>
        <p class="text-sm text-gray-600">Ya apareces en búsquedas. Completa los pasos restantes para mejorar tu posición.</p>
    </div>
    @endif

</div>

@push('scripts')
<script>
function previewOnboarding(input, previewId) {
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