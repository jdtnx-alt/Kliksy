@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto py-6 sm:py-8 px-4 sm:px-6">

    <h1 class="text-2xl sm:text-3xl font-bold mb-6">Mi Perfil</h1>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TARJETA USUARIO --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-5 sm:p-6 mb-6 flex items-center gap-4">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-bold text-lg sm:text-xl flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <h2 class="text-lg sm:text-xl font-bold text-white truncate">{{ auth()->user()->name }}</h2>
            <p class="text-blue-200 text-sm truncate">{{ auth()->user()->email }}</p>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="text-xs bg-white/20 text-white px-3 py-0.5 rounded-full">
                    {{ auth()->user()->role_id === 1 ? 'Cliente' : 'Profesional' }}
                </span>
                @if(auth()->user()->telefono)
                <span class="text-xs text-blue-200 flex items-center gap-1">
                    <i class="bi bi-telephone"></i> {{ auth()->user()->telefono }}
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- INFORMACIÓN PERSONAL --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 mb-5">

        <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2 text-sm sm:text-base">
            <i class="bi bi-person text-blue-500"></i> Información Personal
        </h3>
        <p class="text-xs text-gray-400 mb-5">Estos datos son privados y no se muestran a otros usuarios</p>

        <form method="POST" action="{{ route('perfil.actualizar') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Nombre completo</label>
                <input type="text" name="name"
                    value="{{ old('name', auth()->user()->name) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email"
                    value="{{ old('email', auth()->user()->email) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Teléfono</label>
                <input type="text" name="telefono"
                    value="{{ old('telefono', auth()->user()->telefono) }}"
                    placeholder="Sin teléfono"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                @error('telefono')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer text-sm font-semibold transition shadow-sm shadow-blue-200">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
            </div>

        </form>
    </div>

    {{-- CAMBIAR CONTRASEÑA --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 sm:p-6">

        <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2 text-sm sm:text-base">
            <i class="bi bi-shield-lock text-blue-500"></i> Seguridad
        </h3>
        <p class="text-xs text-gray-400 mb-5">Cambia tu contraseña regularmente para mantener tu cuenta segura</p>

        <form method="POST" action="{{ route('perfil.password') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Contraseña actual</label>
                <input type="password" name="password_actual"
                    placeholder="Tu contraseña actual"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                @error('password_actual')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Nueva contraseña</label>
                <input type="password" name="password"
                    placeholder="Mínimo 6 caracteres"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation"
                    placeholder="Repite la nueva contraseña"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer text-sm font-semibold transition">
                    <i class="bi bi-shield-check"></i> Cambiar Contraseña
                </button>
            </div>

        </form>
    </div>

</div>

@endsection