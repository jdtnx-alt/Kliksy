@extends('layouts.app')
@section('titulo', 'Reservar servicio — Kliksy')
@section('content')

<div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('profesional.publico', $profesional->id) }}"
            class="text-sm text-gray-400 hover:text-blue-600 flex items-center gap-1.5 mb-4 transition">
            <i class="bi bi-arrow-left"></i> Volver al perfil
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Reservar servicio</h1>
        <p class="text-gray-400 text-sm mt-1">Elige fecha y hora para tu cita</p>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 text-sm flex items-center gap-2">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
    @endif

    {{-- CARD PROFESIONAL + SERVICIO --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center font-bold text-blue-600 text-base flex-shrink-0">
            {{ strtoupper(substr($profesional->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-800 text-sm">{{ $profesional->name }}</p>
            <p class="text-blue-600 text-sm font-medium">{{ $servicio->titulo }}</p>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <i class="bi bi-clock"></i> {{ $servicio->duracion }} min
                </span>
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <i class="bi bi-cash"></i> ${{ number_format($servicio->precio, 0, ',', '.') }} COP
                </span>
            </div>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <form method="POST" action="{{ route('reservas.store') }}" id="formReserva">
        @csrf
        <input type="hidden" name="profesional_id" value="{{ $profesional->id }}">
        <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
        <input type="hidden" name="hora_inicio" id="horaSeleccionada">

        {{-- SELECCIONAR SERVICIO si tiene varios --}}
        @if($profesional->servicios->count() > 1)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-4">
            <h3 class="font-semibold text-gray-800 text-sm mb-3 flex items-center gap-2">
                <i class="bi bi-scissors text-blue-500"></i> Selecciona el servicio
            </h3>
            <div class="flex flex-col gap-2">
                @foreach($profesional->servicios as $s)
                <a href="{{ route('reservas.create', [$profesional->id, 'servicio_id' => $s->id]) }}"
                    class="flex items-center justify-between p-3 rounded-xl border transition
                    {{ $s->id === $servicio->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $s->titulo }}</p>
                        <p class="text-xs text-gray-400"><i class="bi bi-clock"></i> {{ $s->duracion }} min</p>
                    </div>
                    <span class="text-blue-600 font-bold text-sm">${{ number_format($s->precio, 0, ',', '.') }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- FECHA --}}
@php
    $perfilPro      = $profesional->perfilProfesional;
    $diasLaborables = $perfilPro?->dias_laborables ?? ['lun','mar','mie','jue','vie'];
    $diasBloqueados = $perfilPro?->dias_bloqueados ?? [];
    $mapaDias       = [0=>'dom',1=>'lun',2=>'mar',3=>'mie',4=>'jue',5=>'vie',6=>'sab'];

    // Generar fechas disponibles para los próximos 30 días
    $fechasDisponibles = [];
    $fechasBloqueadas  = [];
    for ($i = 0; $i <= 30; $i++) {
        $fecha     = now()->setTimezone('America/Bogota')->addDays($i);
        $diaSemana = $mapaDias[$fecha->dayOfWeek];
        $fechaStr  = $fecha->format('Y-m-d');
        if (!in_array($diaSemana, $diasLaborables) || in_array($fechaStr, $diasBloqueados)) {
            $fechasBloqueadas[] = $fechaStr;
        } else {
            $fechasDisponibles[] = $fechaStr;
        }
    }
@endphp

<div class="bg-white border border-gray-200 rounded-2xl p-5 mb-4">
    <h3 class="font-semibold text-gray-800 text-sm mb-3 flex items-center gap-2">
        <i class="bi bi-calendar3 text-blue-500"></i> Selecciona la fecha
    </h3>

    {{-- Días laborables --}}
    <div class="flex gap-1.5 mb-4 flex-wrap">
        @php $nombresDias = ['lun'=>'Lun','mar'=>'Mar','mie'=>'Mié','jue'=>'Jue','vie'=>'Vie','sab'=>'Sáb','dom'=>'Dom']; @endphp
        @foreach($nombresDias as $slug => $nombre)
        <span class="text-xs px-2.5 py-1 rounded-full font-medium
            {{ in_array($slug, $diasLaborables) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400 line-through' }}">
            {{ $nombre }}
        </span>
        @endforeach
    </div>

    <input type="date"
        id="fechaInput"
        min="{{ now()->setTimezone('America/Bogota')->format('Y-m-d') }}"
        max="{{ now()->setTimezone('America/Bogota')->addDays(30)->format('Y-m-d') }}"
        class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
        onchange="validarFecha(this.value)">
    <p id="fechaError" class="text-xs text-red-500 mt-2 hidden">
        <i class="bi bi-exclamation-circle"></i> Este día no está disponible. Por favor elige otra fecha.
    </p>
    <input type="hidden" name="fecha" id="fechaHidden">
</div>

        {{-- SLOTS HORARIOS --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-4" id="seccionSlots" style="display:none">
            <h3 class="font-semibold text-gray-800 text-sm mb-3 flex items-center gap-2">
                <i class="bi bi-clock text-blue-500"></i> Selecciona la hora
            </h3>
            <div id="loadingSlots" class="text-center py-6 text-gray-400 hidden">
                <i class="bi bi-arrow-repeat text-2xl animate-spin block mb-2"></i>
                <p class="text-sm">Cargando horarios...</p>
            </div>
            <div id="gridSlots" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
            <p id="sinSlots" class="text-center text-sm text-gray-400 py-4 hidden">
                No hay horarios disponibles para este día.
            </p>
        </div>

        {{-- NOTA --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6">
            <h3 class="font-semibold text-gray-800 text-sm mb-3 flex items-center gap-2">
                <i class="bi bi-chat-text text-blue-500"></i> Nota para el profesional
                <span class="text-gray-400 font-normal">(opcional)</span>
            </h3>
            <textarea name="nota_cliente" rows="3"
                placeholder="Ej: Necesito el servicio a domicilio en tal dirección..."
                class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"></textarea>
        </div>

        {{-- RESUMEN Y BOTÓN --}}
        <div id="resumenReserva" class="hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-4">
                <p class="text-sm font-semibold text-blue-800 mb-1">Resumen de tu reserva</p>
                <div class="flex flex-col gap-1 text-sm text-blue-700">
                    <span><i class="bi bi-calendar3 mr-1"></i> <span id="resumenFecha"></span></span>
                    <span><i class="bi bi-clock mr-1"></i> <span id="resumenHora"></span></span>
                    <span><i class="bi bi-cash mr-1"></i> ${{ number_format($servicio->precio, 0, ',', '.') }} COP</span>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-base transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2 cursor-pointer">
                <i class="bi bi-lock-fill"></i> Continuar al pago
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
const profesionalId = {{ $profesional->id }};
const servicioId    = {{ $servicio->id }};
let slotSeleccionado = null;

const fechasBloqueadas = @json($fechasBloqueadas);
const fechasDisponibles = @json($fechasDisponibles);

function validarFecha(fecha) {
    if (fechasBloqueadas.includes(fecha)) {
        document.getElementById('fechaError').classList.remove('hidden');
        document.getElementById('fechaHidden').value = '';
        document.getElementById('seccionSlots').style.display = 'none';
        document.getElementById('resumenReserva').classList.add('hidden');
        return;
    }
    document.getElementById('fechaError').classList.add('hidden');
    cargarSlots(fecha);
}

function cargarSlots(fecha) {
    document.getElementById('fechaHidden').value = fecha;
    document.getElementById('seccionSlots').style.display = 'block';
    document.getElementById('loadingSlots').classList.remove('hidden');
    document.getElementById('gridSlots').innerHTML = '';
    document.getElementById('sinSlots').classList.add('hidden');
    document.getElementById('resumenReserva').classList.add('hidden');
    document.getElementById('horaSeleccionada').value = '';
    slotSeleccionado = null;

    fetch(`/reservar/${profesionalId}/slots?fecha=${fecha}&servicio_id=${servicioId}`)
        .then(r => r.json())
        .then(slots => {
            document.getElementById('loadingSlots').classList.add('hidden');

            if (!slots.length) {
                document.getElementById('sinSlots').classList.remove('hidden');
                return;
            }

            const grid = document.getElementById('gridSlots');
            grid.innerHTML = '';

            slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.inicio = slot.hora_inicio;
                btn.dataset.fin    = slot.hora_fin;
                btn.dataset.label  = slot.label;
                btn.textContent    = slot.label;

                if (!slot.disponible) {
                    btn.className = 'px-3 py-2.5 rounded-xl text-xs font-medium border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed';
                    btn.disabled = true;
                } else {
                    btn.className = 'px-3 py-2.5 rounded-xl text-xs font-medium border border-gray-200 bg-white text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-600 transition cursor-pointer';
                    btn.onclick = () => seleccionarSlot(btn, fecha);
                }

                grid.appendChild(btn);
            });
        });
}

function seleccionarSlot(btn, fecha) {
    // Deseleccionar anterior
    document.querySelectorAll('#gridSlots button.seleccionado').forEach(b => {
        b.classList.remove('seleccionado', 'bg-blue-600', 'text-white', 'border-blue-600');
        b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
    });

    // Seleccionar nuevo
    btn.classList.add('seleccionado', 'bg-blue-600', 'text-white', 'border-blue-600');
    btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');

    document.getElementById('horaSeleccionada').value = btn.dataset.inicio;
    slotSeleccionado = btn.dataset;

    // Mostrar resumen
    const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-CO', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    document.getElementById('resumenFecha').textContent = fechaFormateada;
    document.getElementById('resumenHora').textContent  = btn.dataset.label;
    document.getElementById('resumenReserva').classList.remove('hidden');
}
</script>
@endpush

@endsection