@extends('layouts.app')
@section('titulo', 'Pago — Kliksy')
@section('content')

<div class="max-w-lg mx-auto px-4 py-8 sm:py-12">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Confirmar y pagar</h1>
        <p class="text-gray-400 text-sm mt-1">Completa el pago para confirmar tu reserva</p>
    </div>

    {{-- RESUMEN --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6">
        <h3 class="font-semibold text-gray-800 text-sm mb-4">Resumen de la reserva</h3>
        <div class="flex flex-col gap-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Servicio</span>
                <span class="font-medium text-gray-800">{{ $reserva->servicio->titulo }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Profesional</span>
                <span class="font-medium text-gray-800">{{ $reserva->profesional->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fecha</span>
                <span class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Hora</span>
                <span class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('g:i A') }} –
                    {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('g:i A') }}
                </span>
            </div>
            <div class="border-t border-gray-100 pt-3 mt-1 flex justify-between">
                <span class="font-bold text-gray-800">Total</span>
                <span class="font-bold text-blue-600 text-lg">${{ number_format($reserva->monto, 0, ',', '.') }} COP</span>
            </div>
        </div>
    </div>

    {{-- FORMULARIO PAGO SIMULADO --}}
    <form method="POST" action="{{ route('reservas.pago.procesar', $reserva->id) }}"
        x-data="{ procesando: false }" @submit.prevent="if(!procesando) { procesando = true; setTimeout(() => { $el.submit(); }, 1500) }">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-2 mb-5">
                <h3 class="font-semibold text-gray-800 text-sm">Datos de pago</h3>
                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-medium">
                    Simulado
                </span>
            </div>

            {{-- NÚMERO DE TARJETA --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Número de tarjeta</label>
                <div class="relative">
                    <input type="text" name="numero_tarjeta"
                        placeholder="1234 5678 9012 3456"
                        maxlength="19"
                        value="{{ old('numero_tarjeta') }}"
                        oninput="formatCard(this)"
                        class="w-full border border-gray-200 rounded-xl p-3 pl-10 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <i class="bi bi-credit-card absolute left-3 top-3 text-gray-400"></i>
                </div>
                @error('numero_tarjeta')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NOMBRE --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre en la tarjeta</label>
                <input type="text" name="nombre_tarjeta"
                    placeholder="JUAN GARCIA"
                    value="{{ old('nombre_tarjeta') }}"
                    class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition uppercase">
                @error('nombre_tarjeta')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- VENCIMIENTO --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Vencimiento</label>
                    <input type="text" name="vencimiento"
                        placeholder="MM/AA"
                        maxlength="5"
                        value="{{ old('vencimiento') }}"
                        oninput="formatExpiry(this)"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    @error('vencimiento')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- CVV --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">CVV</label>
                    <input type="text" name="cvv"
                        placeholder="123"
                        maxlength="3"
                        value="{{ old('cvv') }}"
                        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    @error('cvv')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center mb-4 flex items-center justify-center gap-1.5">
            <i class="bi bi-shield-lock text-gray-300"></i>
            Pago simulado con fines educativos. No se realizan cargos reales.
        </p>

        <button type="submit"
            :disabled="procesando"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white py-4 rounded-2xl font-bold text-base transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2 cursor-pointer">
            <span x-show="!procesando" class="flex items-center gap-2">
                <i class="bi bi-lock-fill"></i>
                Pagar ${{ number_format($reserva->monto, 0, ',', '.') }} COP
            </span>
            <span x-show="procesando" class="flex items-center gap-2">
                <i class="bi bi-arrow-repeat animate-spin"></i>
                Procesando pago...
            </span>
        </button>

    </form>
</div>

@push('scripts')
<script>
function formatCard(input) {
    let val = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = val.replace(/(.{4})/g, '$1 ').trim();
    // Para validación necesitamos solo dígitos
    input.dataset.raw = val;
}
function formatExpiry(input) {
    let val = input.value.replace(/\D/g, '').substring(0, 4);
    if (val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
    input.value = val;
}

// Prevenir que el Enter en los inputs envíe el formulario antes de tiempo
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});

// Antes de enviar, quitar espacios del número de tarjeta
document.querySelector('form').addEventListener('submit', function() {
    const cardInput = document.querySelector('input[name="numero_tarjeta"]');
    cardInput.value = cardInput.value.replace(/\s/g, '');
});
</script>
@endpush

@endsection