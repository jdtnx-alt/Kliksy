@extends('layouts.profesional')
@section('title', 'Servicios')
@section('page_title', 'Mis Servicios')

@section('content')
@php
    use App\Helpers\CategoriaHelper;
    $arbolCats = CategoriaHelper::arbol();
    $servicios = \App\Models\Servicio::where('user_id', auth()->id())->latest()->get();
@endphp

<div x-data="{
    openModal: false,
    modo: 'crear',
    servicioId: null,
    titulo: '',
    descripcion: '',
    precio: '',
    categoria: '',
    subcategoria: '',
    duracion: '60'
}">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $servicios->count() }} servicios publicados</p>
        <button @click="openModal = true; modo = 'crear'; titulo = ''; descripcion = ''; precio = ''; categoria = ''; subcategoria = ''; duracion = '60';"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl cursor-pointer text-sm font-semibold transition shadow-sm shadow-blue-200 flex items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> Nuevo servicio
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- LISTA --}}
    @if($servicios->isEmpty())
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center text-gray-400">
        <i class="bi bi-scissors text-5xl mb-4 block"></i>
        <p class="font-medium text-gray-500">Aún no tienes servicios</p>
        <p class="text-sm mt-1">Publica tu primer servicio para que los clientes puedan contactarte</p>
        <button @click="openModal = true; modo = 'crear'; titulo = ''; descripcion = ''; precio = ''; categoria = ''; subcategoria = ''; duracion = '60';"
            class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer">
            + Publicar primer servicio
        </button>
    </div>
    @else
    <div class="flex flex-col gap-3">
        @foreach($servicios as $servicio)
        @php
            $fotosCard = $servicio->fotos ?? ($servicio->foto ? [$servicio->foto] : []);
            if (empty($fotosCard)) {
                $perfilFotos = $servicio->user->perfilProfesional?->fotos;
                if ($perfilFotos && $perfilFotos->count() > 0) {
                    $fotosCard = $perfilFotos->pluck('ruta')->toArray();
                }
            }
        @endphp
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-blue-200 hover:shadow-sm transition">

            {{-- INFO --}}
            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full font-medium">
                                {{ CategoriaHelper::nombre($servicio->subcategoria ?: $servicio->categoria) }}
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-800 text-sm sm:text-base">{{ $servicio->titulo }}</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">{{ $servicio->descripcion }}</p>

@if(count($fotosCard) > 0)
<div x-data="{
    lightbox: false,
    indice: 0,
    fotos: {{ json_encode(array_map(fn($f) => asset('storage/' . $f), $fotosCard)) }},
    get total() { return this.fotos.length },
    anterior() { this.indice = (this.indice - 1 + this.total) % this.total },
    siguiente() { this.indice = (this.indice + 1) % this.total }
}">
    <button @click="lightbox = true; indice = 0"
        class="mt-2 flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-700 transition cursor-pointer">
        <i class="bi bi-images"></i>
        Ver {{ count($fotosCard) }} foto{{ count($fotosCard) > 1 ? 's' : '' }}
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
                    <div class="flex items-center justify-between sm:justify-end gap-4 flex-shrink-0">
                        <div class="text-right">
                            <span class="text-blue-600 font-bold text-lg block">
                                ${{ number_format($servicio->precio, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-400">
                                <i class="bi bi-clock"></i> {{ $servicio->duracion ?? 60 }} min
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openModal = true; modo = 'editar'; servicioId = {{ $servicio->id }}; titulo = '{{ addslashes($servicio->titulo) }}'; descripcion = '{{ addslashes($servicio->descripcion) }}'; precio = '{{ $servicio->precio }}'; categoria = '{{ $servicio->categoria }}'; subcategoria = '{{ $servicio->subcategoria }}'; duracion = '{{ $servicio->duracion ?? 60 }}';"
                                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer">
                                <i class="bi bi-pencil text-sm"></i>
                            </button>
                            <form action="{{ route('profesional.servicios.destroy', $servicio->id) }}" method="POST">
                                @csrf @method('DELETE')
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

    {{-- MODAL --}}
    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4" style="display:none">
        <div @click.away="openModal=false" class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-gray-800" x-text="modo === 'crear' ? 'Nuevo servicio' : 'Editar servicio'"></h2>
                    <p class="text-xs text-gray-400 mt-0.5">Los clientes verán esta información en tu perfil</p>
                </div>
                <button @click="openModal=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition cursor-pointer">✕</button>
            </div>
            <form method="POST" enctype="multipart/form-data"
                :action="modo === 'crear' ? '{{ route('profesional.servicios.store') }}' : '/profesional/servicios/' + servicioId">
                @csrf
                <template x-if="modo === 'editar'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Título</label>
                    <input type="text" name="titulo" x-model="titulo" placeholder="Ej: Corte de cabello a domicilio"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="3" x-model="descripcion" placeholder="Describe qué incluye el servicio..."
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Categoría</label>
                    <select name="categoria" x-model="categoria" @change="subcategoria = ''"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">Seleccionar categoría</option>
                        @foreach($arbolCats as $slug => $cat)
                        <option value="{{ $slug }}">{{ $cat['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4" x-show="categoria !== ''" x-cloak>
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Subcategoría <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <select name="subcategoria" x-model="subcategoria"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">Todas las subcategorías</option>
                        @foreach($arbolCats as $slug => $cat)
                        @foreach($cat['subs'] as $subSlug => $subNombre)
                        <option value="{{ $subSlug }}" x-show="categoria === '{{ $slug }}'">{{ $subNombre }}</option>
                        @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">Precio (COP)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-gray-400 text-sm">$</span>
                        <input type="number" name="precio" x-model="precio" placeholder="0"
                            class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">
                        Duración estimada
                        <span class="text-gray-400 font-normal">(minutos)</span>
                    </label>
                    <select name="duracion" x-model="duracion"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="15">15 minutos</option>
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60">1 hora</option>
                        <option value="75">1 hora 15 minutos</option>
                        <option value="90">1 hora 30 minutos</option>
                        <option value="105">1 hora 45 minutos</option>
                        <option value="120">2 horas</option>
                        <option value="135">2 horas 15 minutos</option>
                        <option value="150">2 horas 30 minutos</option>
                        <option value="165">2 horas 45 minutos</option>
                        <option value="180">3 horas</option>
                        <option value="210">3 horas 30 minutos</option>
                        <option value="240">4 horas</option>
                        <option value="300">5 horas</option>
                        <option value="360">Medio día (6 horas)</option>
                        <option value="480">Todo el día (Laboral - 8 horas)</option>
                        <option value="720">Todo el día (12 horas)</option>
                        <option value="1440">Todo el día (24 horas)</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block mb-1.5 text-sm font-semibold text-gray-700">
                        Fotos del servicio
                        <span class="text-gray-400 font-normal">(máx. 5 fotos)</span>
                    </label>
                    <div x-data="{ previews: [] }">
                        <label for="modalFotosServicio" class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer block">
    <template x-if="previews.length === 0">
        <div>
            <i class="bi bi-images text-2xl text-gray-300 mb-1 block"></i>
            <p class="text-xs text-gray-500 font-medium">Haz clic para subir fotos</p>
            <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — máx. 2MB por foto, hasta 5 fotos</p>
        </div>
    </template>
    <template x-if="previews.length > 0">
        <div class="grid grid-cols-3 gap-2">
            <template x-for="src in previews" :key="src">
                <img :src="src" class="w-full h-20 object-cover rounded-lg">
            </template>
        </div>
    </template>
    <input type="file" id="modalFotosServicio" name="fotos[]"
        accept="image/*" multiple class="hidden"
        @change="
            previews = [];
            const files = Array.from($event.target.files).slice(0, 5);
            files.forEach(f => {
                const r = new FileReader();
                r.onload = e => previews.push(e.target.result);
                r.readAsDataURL(f);
            });
        ">
</label>
                        <template x-if="previews.length > 0">
                            <p class="text-xs text-gray-400 mt-2 text-center">
                                <span x-text="previews.length"></span> foto(s) seleccionada(s)
                            </p>
                        </template>
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

</div>
@endsection