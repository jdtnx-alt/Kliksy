@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-8 px-4">

    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-sm">

        <div class="text-center mb-6 sm:mb-8">
            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-blue-600 rounded-xl flex items-center justify-center font-black text-white text-lg mx-auto mb-4">K</div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Nueva contraseña</h1>
            <p class="text-gray-500 text-sm mt-1">Ingresa tu nueva contraseña para recuperar el acceso</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required
                    value="{{ old('email') }}"
                    placeholder="tu@correo.com"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Nueva contraseña</label>
                <input type="password" name="password" required
                    placeholder="Mínimo 6 caracteres"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Repite tu contraseña"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 sm:py-3.5 rounded-xl font-bold transition text-sm sm:text-base">
                Actualizar contraseña
            </button>

            <p class="text-center text-sm text-gray-400 mt-4">
                <a href="{{ route('inicio') }}" class="text-blue-600 hover:underline">Volver al inicio</a>
            </p>

        </form>
    </div>
</div>

@endsection