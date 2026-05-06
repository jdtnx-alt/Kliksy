@extends('layouts.profesional')
@section('title', 'Negocio')
@section('page_title', 'Mi Negocio Físico')

@section('content')
@php
    use App\Helpers\CategoriaHelper;
    $categorias = CategoriaHelper::arbol();
    $perfil  = auth()->user()->perfilProfesional;
    $negocio = $perfil?->negocio;
@endphp

<div x-data="{ tieneNegocio: {{ $negocio ? 'true' : 'false' }} }">

    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <h2 class="text-base font-bold text-gray-800 mb-1">Negocio físico</h2>
        <p class="text-sm text-gray-400 mb-6">Registra tu negocio para que los clientes puedan visitarte</p>

        {{-- TOGGLE --}}
        <div class="flex items-center gap-3 mb-6 p-4 bg-gray-50 rounded-xl">
            <button type="button" @click="tieneNegocio = !tieneNegocio"
                :class="tieneNegocio ? 'bg-blue-500' : 'bg-gray-300'"
                class="relative w-12 h-6 rounded-full transition-colors duration-200 cursor-pointer flex-shrink-0">
                <span :class="tieneNegocio ? 'translate-x-6' : 'translate-x-1'"
                    class="absolute top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm block"></span>
            </button>
            <div>
                <span class="font-semibold text-gray-700 text-sm">Tengo negocio físico</span>
                <p class="text-xs text-gray-400 mt-0.5">Los clientes podrán ver tu dirección y visitarte</p>
            </div>
        </div>

        <div x-show="tieneNegocio" x-cloak>
            <form method="POST" action="{{ route('negocio.guardar') }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Nombre del negocio</label>
                    <input type="text" name="nombre" placeholder="Ej: Barbería El Estilo"
                        value="{{ old('nombre', $negocio->nombre ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="3" placeholder="Cuéntale a los clientes sobre tu negocio..."
                        class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"
                    >{{ old('descripcion', $negocio->descripcion ?? '') }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Dirección</label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-3.5 top-3.5 text-blue-400 text-sm"></i>
                        <input type="text" name="direccion" placeholder="Ej: Calle 10 #5-23, Florencia"
                            value="{{ old('direccion', $negocio->direccion ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Teléfono</label>
                        <input type="text" name="telefono" placeholder="Ej: 3001234567"
                            value="{{ old('telefono', $negocio->telefono ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700">Categoría principal</label>
                        <select name="categoria"
                            class="w-full border border-gray-200 rounded-xl p-3.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                            <option value="">Seleccionar</option>
                            @foreach($categorias as $valor => $cat)
                            <option value="{{ $valor }}" {{ old('categoria', $negocio->categoria ?? '') === $valor ? 'selected' : '' }}>
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
@endsection