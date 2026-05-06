@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12 px-4">

    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-2xl mx-auto mb-5">
            K
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">¡Bienvenido a Kliksy!</h1>
        <p class="text-gray-500 text-sm">Una última pregunta antes de continuar</p>
    </div>

    <form method="POST" action="{{ route('auth.google.rol.guardar') }}">
        @csrf
        <div class="flex flex-col gap-4 mb-6">

            <label class="flex items-center gap-4 border-2 border-slate-200 hover:border-blue-400 rounded-2xl px-5 py-4 cursor-pointer transition-all duration-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                <input type="radio" name="role" value="cliente" class="accent-blue-600 w-4 h-4">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Soy cliente</p>
                    <p class="text-xs text-gray-500 mt-0.5">Busco profesionales para mis necesidades</p>
                </div>
            </label>

            <label class="flex items-center gap-4 border-2 border-slate-200 hover:border-blue-400 rounded-2xl px-5 py-4 cursor-pointer transition-all duration-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                <input type="radio" name="role" value="profesional" class="accent-blue-600 w-4 h-4">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v4M10 14h4"/></svg>
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Soy profesional</p>
                    <p class="text-xs text-gray-500 mt-0.5">Ofrezco mis servicios en la plataforma</p>
                </div>
            </label>

        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-blue-200">
            Continuar
        </button>
    </form>

</div>
@endsection