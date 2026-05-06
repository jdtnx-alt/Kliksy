@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-8 px-4">

    <div class="bg-white border border-gray-300 rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-sm">

        <div class="flex justify-center mb-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 text-white rounded-lg flex items-center justify-center text-xl sm:text-2xl font-bold">
                K
            </div>
        </div>

        <h2 class="text-xl sm:text-2xl font-bold text-center mb-2">Crear Cuenta</h2>
        <p class="text-gray-500 text-center text-sm mb-6">
            Regístrate para acceder a todos los servicios de Kliksy
        </p>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Tu nombre"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="+57 311 522 9975"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" placeholder="Repite tu contraseña"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Tipo de cuenta</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="border-2 border-gray-300 rounded-lg p-3 flex items-start gap-2 sm:gap-3 cursor-pointer hover:border-blue-400 transition">
                        <input type="radio" name="role" value="cliente" class="mt-1 flex-shrink-0"
                            {{ old('role', 'cliente') == 'cliente' ? 'checked' : '' }}>
                        <div>
                            <p class="font-medium text-sm">Cliente</p>
                            <p class="text-xs text-gray-500">Busco servicios</p>
                        </div>
                    </label>
                    <label class="border-2 border-gray-300 rounded-lg p-3 flex items-start gap-2 sm:gap-3 cursor-pointer hover:border-blue-400 transition">
                        <input type="radio" name="role" value="profesional" class="mt-1 flex-shrink-0"
                            {{ old('role') == 'profesional' ? 'checked' : '' }}>
                        <div>
                            <p class="font-medium text-sm">Profesional</p>
                            <p class="text-xs text-gray-500">Ofrezco servicios</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg mt-2 transition font-medium text-sm sm:text-base">
                Crear Cuenta
            </button>

            <p class="text-center text-sm text-gray-500 pt-2">
                ¿Ya tienes cuenta?
                <button type="button" onclick="openLogin()"
                    class="text-blue-600 font-semibold hover:underline bg-transparent border-none cursor-pointer">
                    Inicia sesión
                </button>
            </p>

        </form>
    </div>
</div>

@endsection