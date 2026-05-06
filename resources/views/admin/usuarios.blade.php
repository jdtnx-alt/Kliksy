@extends('admin.layout')
@section('title', 'Usuarios')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 sm:mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Usuarios</h1>
        <p class="text-gray-400 text-sm mt-1">Gestiona los usuarios registrados en la plataforma</p>
    </div>
    <button onclick="document.getElementById('modalAdmin').classList.remove('hidden')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 cursor-pointer self-start sm:self-auto">
        <i class="bi bi-person-plus"></i>
        <span>Nuevo administrador</span>
    </button>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
    {{ session('success') }}
</div>
@endif

{{-- TABLA — scroll horizontal en móvil --}}
<div class="bg-white border border-gray-300 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="border-b border-gray-300">
                <tr>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">#</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Nombre</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Correo</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Rol</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Registro</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($usuarios as $usuario)
                <tr id="item-usuario-{{ $usuario->id }}" class="hover:bg-gray-50 transition">
                    <td class="px-4 sm:px-6 py-4 text-gray-300">{{ $usuario->id }}</td>
                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($usuario->name, 0, 2)) }}
                            </div>
                            <button onclick="verUsuario({{ $usuario->id }})"
                                class="font-medium text-gray-800 hover:text-blue-600 transition cursor-pointer text-left">
                                {{ $usuario->name }}
                            </button>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-gray-400 text-xs sm:text-sm">{{ $usuario->email }}</td>
                    <td class="px-4 sm:px-6 py-4">
                        @if($usuario->role_id === 1)
                            <span class="bg-blue-50 text-blue-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Cliente</span>
                        @elseif($usuario->role_id === 2)
                            <span class="bg-green-50 text-green-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Profesional</span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-gray-400 text-xs">{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 sm:px-6 py-4">
                        <form method="POST" action="{{ route('admin.usuarios.eliminar', $usuario->id) }}"
                            onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition text-sm flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-trash"></i>
                                <span class="hidden sm:inline">Eliminar</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-end">
    {{ $usuarios->links() }}
</div>

{{-- MODAL CREAR ADMIN --}}
<div id="modalAdmin"
    class="fixed inset-0 bg-gray-900/45 z-[100] hidden flex items-center justify-center p-4"
    onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Nuevo administrador</h2>
                <p class="text-sm text-gray-400 mt-0.5">Crea un usuario con rol de admin</p>
            </div>
            <button onclick="document.getElementById('modalAdmin').classList.add('hidden')"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition cursor-pointer flex-shrink-0">
                ✕
            </button>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.admins.guardar') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Nombre</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition cursor-pointer text-sm">
                    Crear administrador
                </button>
                <button type="button"
                    onclick="document.getElementById('modalAdmin').classList.add('hidden')"
                    class="px-4 sm:px-6 py-3 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition text-sm font-medium cursor-pointer">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

@include('admin.partials.usuario_drawer')

@endsection