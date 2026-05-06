@extends('admin.layout')
@section('title', 'Detalle de Usuario')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.usuarios') }}" class="flex items-center gap-2 text-gray-400 hover:text-gray-700 text-sm mb-4">
        <i class="bi bi-arrow-left"></i> Volver a usuarios
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Detalle de usuario</h1>
    <p class="text-gray-400 text-sm mt-1">Información completa del usuario</p>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- COLUMNA IZQUIERDA --}}
    <div class="lg:col-span-1 flex flex-col gap-5">

        {{-- TARJETA USUARIO --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl flex-shrink-0">
                    {{ strtoupper(substr($usuario->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-base">{{ $usuario->name }}</h2>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                        {{ $usuario->role_id === 1 ? 'bg-blue-50 text-blue-600' : ($usuario->role_id === 2 ? 'bg-green-50 text-green-600' : 'bg-purple-50 text-purple-600') }}">
                        {{ $usuario->role_id === 1 ? 'Cliente' : ($usuario->role_id === 2 ? 'Profesional' : 'Admin') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-3 text-sm text-gray-600">
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-envelope text-blue-400 flex-shrink-0"></i>
                    <span class="truncate">{{ $usuario->email }}</span>
                </div>
                @if($usuario->telefono)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-telephone text-blue-400 flex-shrink-0"></i>
                    {{ $usuario->telefono }}
                </div>
                @endif
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-calendar text-blue-400 flex-shrink-0"></i>
                    Registrado {{ $usuario->created_at->diffForHumans() }}
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-patch-check {{ $usuario->email_verified_at ? 'text-green-500' : 'text-gray-300' }} flex-shrink-0"></i>
                    {{ $usuario->email_verified_at ? 'Correo verificado' : 'Correo sin verificar' }}
                </div>
            </div>

            {{-- ELIMINAR USUARIO --}}
            <form method="POST" action="{{ route('admin.usuarios.eliminar', $usuario->id) }}"
                onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')"
                class="mt-5">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full bg-red-50 hover:bg-red-100 text-red-500 border border-red-200 text-sm py-2.5 rounded-xl transition cursor-pointer font-medium">
                    <i class="bi bi-trash mr-1.5"></i> Eliminar usuario
                </button>
            </form>
        </div>

        {{-- PERFIL PROFESIONAL --}}
        @if($usuario->role_id === 2 && $usuario->perfilProfesional)
        @php $perfil = $usuario->perfilProfesional; @endphp
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-person-badge text-blue-500"></i> Perfil profesional
            </h3>
            <div class="flex flex-col gap-3 text-sm text-gray-600">
                @if($perfil->ubicacion)
                <div class="flex items-start gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-geo-alt text-blue-400 flex-shrink-0 mt-0.5"></i>
                    {{ $perfil->ubicacion }}
                </div>
                @endif
                @if($perfil->experiencia)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-briefcase text-blue-400 flex-shrink-0"></i>
                    {{ $perfil->experiencia }}
                </div>
                @endif
                @if($perfil->whatsapp)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-whatsapp text-green-500 flex-shrink-0"></i>
                    +{{ $perfil->whatsapp }}
                </div>
                @endif
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <i class="bi bi-moon {{ $perfil->en_vacaciones ? 'text-orange-400' : 'text-gray-300' }} flex-shrink-0"></i>
                    {{ $perfil->en_vacaciones ? 'En vacaciones' : 'Disponible' }}
                </div>
                @if($perfil->categorias && count($perfil->categorias) > 0)
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 mb-2">Categorías</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($perfil->categorias as $cat)
                        <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full">{{ ucfirst($cat) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($perfil->descripcion)
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 mb-1">Descripción</p>
                    <p class="text-sm text-gray-600">{{ $perfil->descripcion }}</p>
                </div>
                @endif
            </div>

            {{-- CÉDULA --}}
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Verificación de identidad</p>
                <div class="flex gap-3">
                    <div class="flex-1 bg-gray-50 rounded-xl p-3 text-center">
                        <i class="bi bi-{{ $perfil->cedula_frontal ? 'check-circle-fill text-green-500' : 'x-circle text-gray-300' }} text-lg mb-1 block"></i>
                        <p class="text-xs text-gray-500">Cédula frontal</p>
                    </div>
                    <div class="flex-1 bg-gray-50 rounded-xl p-3 text-center">
                        <i class="bi bi-{{ $perfil->cedula_trasera ? 'check-circle-fill text-green-500' : 'x-circle text-gray-300' }} text-lg mb-1 block"></i>
                        <p class="text-xs text-gray-500">Cédula trasera</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- COLUMNA DERECHA --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- SERVICIOS --}}
        @if($usuario->role_id === 2)
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-scissors text-blue-500"></i>
                Servicios publicados
                <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">{{ $usuario->servicios->count() }}</span>
            </h3>
            @forelse($usuario->servicios as $servicio)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $servicio->titulo }}</p>
                    <p class="text-xs text-gray-400">{{ \App\Helpers\CategoriaHelper::nombre($servicio->subcategoria ?: $servicio->categoria) }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                    <span class="text-sm font-bold text-blue-600">${{ number_format($servicio->precio, 0, ',', '.') }}</span>
                    <form method="POST" action="{{ route('admin.servicios.eliminar', $servicio->id) }}"
                        onsubmit="return confirm('¿Eliminar este servicio?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500 transition cursor-pointer">
                            <i class="bi bi-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Sin servicios publicados</p>
            @endforelse
        </div>
        @endif

        {{-- SOLICITUDES COMO CLIENTE --}}
        @if($solicitudesCliente->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-bag text-blue-500"></i>
                Solicitudes como cliente
                <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">{{ $solicitudesCliente->count() }}</span>
            </h3>
            @foreach($solicitudesCliente as $sol)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $sol->servicio->titulo ?? 'Servicio eliminado' }}</p>
                    <p class="text-xs text-gray-400">{{ $sol->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-medium flex-shrink-0 ml-3
                    {{ $sol->estado === 'completada' ? 'bg-green-50 text-green-600' :
                       ($sol->estado === 'pendiente' ? 'bg-yellow-50 text-yellow-600' :
                       ($sol->estado === 'cancelada' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-600')) }}">
                    {{ ucfirst($sol->estado) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- SOLICITUDES COMO PROFESIONAL --}}
        @if($solicitudesProfesional->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-briefcase text-green-500"></i>
                Solicitudes como profesional
                <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">{{ $solicitudesProfesional->count() }}</span>
            </h3>
            @foreach($solicitudesProfesional as $sol)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $sol->servicio->titulo ?? 'Servicio eliminado' }}</p>
                    <p class="text-xs text-gray-400">{{ $sol->cliente->name ?? '—' }} · {{ $sol->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-medium flex-shrink-0 ml-3
                    {{ $sol->estado === 'completada' ? 'bg-green-50 text-green-600' :
                       ($sol->estado === 'pendiente' ? 'bg-yellow-50 text-yellow-600' :
                       ($sol->estado === 'cancelada' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-600')) }}">
                    {{ ucfirst($sol->estado) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- RESEÑAS --}}
        @if($usuario->role_id === 2 && $usuario->resenas->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-star text-yellow-400"></i>
                Reseñas recibidas
                <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">{{ $usuario->resenas->count() }}</span>
            </h3>
            @foreach($usuario->resenas as $resena)
            <div class="py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-medium text-gray-800">{{ $resena->cliente->name ?? '—' }}</p>
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-xs {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                        @endfor
                    </div>
                </div>
                <p class="text-xs text-gray-400">{{ $resena->comentario }}</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- SIN ACTIVIDAD --}}
        @if($usuario->servicios->count() === 0 && $solicitudesCliente->count() === 0 && $solicitudesProfesional->count() === 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
            <i class="bi bi-inbox text-4xl mb-3 block"></i>
            <p class="text-sm">Este usuario no tiene actividad registrada aún.</p>
        </div>
        @endif

    </div>
</div>

@endsection