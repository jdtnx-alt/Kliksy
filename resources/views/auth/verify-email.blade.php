@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12 px-4">

    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-2xl mx-auto mb-5">
            K
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Verifica tu correo</h1>
        <p class="text-gray-500 text-sm">
            Te enviamos un enlace de verificación a tu correo electrónico. Revisa tu bandeja de entrada y haz clic en el enlace.
        </p>
    </div>

    @if(session('status') === 'verification-link-sent')
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        Correo reenviado correctamente. Revisa tu bandeja de entrada.
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-5">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="bi bi-envelope text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm mb-1">Revisa tu correo</p>
                <p class="text-gray-500 text-xs">
                    Enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>. 
                    Si no lo ves, revisa la carpeta de spam.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition text-sm">
                <i class="bi bi-arrow-clockwise mr-1.5"></i> Reenviar correo de verificación
            </button>
        </form>
    </div>

    <div class="text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition cursor-pointer bg-transparent border-none">
                Cerrar sesión
            </button>
        </form>
    </div>

</div>
@endsection