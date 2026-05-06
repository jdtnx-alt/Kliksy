@extends('layouts.profesional')
@section('title', 'Mi Perfil')
@section('page_title', 'Mi Perfil')

@section('content')
@php
    use App\Helpers\CategoriaHelper;
    $arbolCats = CategoriaHelper::arbol();
    $perfil = auth()->user()->perfilProfesional;
    $fotos  = $perfil?->fotos ?? collect();

    // Prevenir TypeError: in_array() expects array, string given
    $categoriasUsuario = $perfil ? $perfil->categorias : [];
    if (is_string($categoriasUsuario)) {
        $categoriasUsuario = json_decode($categoriasUsuario, true);
    }
    if (!is_array($categoriasUsuario)) {
        $categoriasUsuario = [];
    }
    
    $categoriasSeleccionadas = session()->hasOldInput() ? old('categorias') : $categoriasUsuario;
    if (!is_array($categoriasSeleccionadas)) {
        $categoriasSeleccionadas = [];
    }
@endphp

<div>

    {{-- PERFIL PROFESIONAL --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
        <h2 class="text-base font-bold text-gray-800 mb-1">Información profesional</h2>
        <p class="text-sm text-gray-400 mb-6">Esta información es visible para todos los clientes</p>

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
                <p class="text-xs text-gray-400 mb-3">Selecciona las categorías en las que ofreces servicios</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    @foreach($arbolCats as $padreSlug => $padre)
    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 transition
                            {{ in_array($padreSlug, $categoriasSeleccionadas) ? 'bg-blue-50' : 'bg-white' }}">
                            <input type="checkbox" name="categorias[]" value="{{ $padreSlug }}"
                                class="accent-blue-500 w-4 h-4"
                                {{ in_array($padreSlug, $categoriasSeleccionadas) ? 'checked' : '' }}>
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
    </div>

    {{-- DATOS PERSONALES --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <h2 class="text-base font-bold text-gray-800 mb-1">Datos personales</h2>
        <p class="text-sm text-gray-400 mb-6">Información privada de tu cuenta</p>
        <form method="POST" action="{{ route('perfil.actualizar') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl cursor-pointer text-sm font-semibold transition">
                    <i class="bi bi-check-lg mr-1.5"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>

</div>

{{-- HORARIO --}}
<div class="bg-white border border-gray-200 rounded-2xl p-6 mt-6" x-data="{ 
    diasBloqueados: {{ json_encode($perfil?->dias_bloqueados ?? []) }},
    nuevaFecha: ''
}">
    <h2 class="text-base font-bold text-gray-800 mb-1">Horario de atención</h2>
    <p class="text-sm text-gray-400 mb-6">Define cuándo estás disponible para recibir reservas</p>

    <form method="POST" action="{{ route('profesional.horario.guardar') }}">
        @csrf

        {{-- DÍAS LABORABLES --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Días laborables</label>
            <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                @php
                    $dias = ['lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue', 'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom'];
                    $diasGuardados = $perfil ? $perfil->dias_laborables : null;
                    if (is_string($diasGuardados)) $diasGuardados = json_decode($diasGuardados, true);
                    if (!is_array($diasGuardados) || empty($diasGuardados)) {
                        $diasGuardados = ['lun','mar','mie','jue','vie']; // Default
                    }
                @endphp
                @foreach($dias as $valor => $label)
                <label class="cursor-pointer">
                    <input type="checkbox" name="dias_laborables[]" value="{{ $valor }}"
                        class="hidden peer"
                        {{ in_array($valor, $diasGuardados) ? 'checked' : '' }}>
                    <div class="text-center py-2.5 rounded-xl border text-sm font-medium transition
                        peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600
                        border-gray-200 text-gray-500 hover:border-blue-300">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- HORAS --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora de inicio</label>
                <input type="time" name="hora_inicio"
                    value="{{ $perfil?->hora_inicio ?? '08:00' }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hora de fin</label>
                <input type="time" name="hora_fin"
                    value="{{ $perfil?->hora_fin ?? '18:00' }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
        </div>

        {{-- DÍAS BLOQUEADOS --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Días bloqueados</label>
            <p class="text-xs text-gray-400 mb-3">Fechas específicas en las que no atiendes — vacaciones, festivos, etc.</p>

            <div class="flex gap-2 mb-3">
                <input type="date" x-model="nuevaFecha"
                    min="{{ now()->toDateString() }}"
                    class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <button type="button"
                    @click="if(nuevaFecha && !diasBloqueados.includes(nuevaFecha)) { diasBloqueados.push(nuevaFecha); nuevaFecha = ''; }"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer">
                    + Agregar
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                <template x-for="(fecha, i) in diasBloqueados" :key="i">
                    <div class="flex items-center gap-1.5 bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-1.5 rounded-full">
                        <span x-text="new Date(fecha + 'T00:00:00').toLocaleDateString('es-CO', {day:'numeric', month:'short', year:'numeric'})"></span>
                        <button type="button" @click="diasBloqueados.splice(i, 1)" class="hover:text-red-900 cursor-pointer font-bold">×</button>
                        <input type="hidden" :name="'dias_bloqueados[]'" :value="fecha">
                    </div>
                </template>
                <p x-show="diasBloqueados.length === 0" class="text-xs text-gray-400">Sin días bloqueados</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl cursor-pointer text-sm font-semibold transition shadow-sm shadow-blue-200">
                <i class="bi bi-check-lg mr-1.5"></i>Guardar horario
            </button>
        </div>
    </form>
</div>
@endsection