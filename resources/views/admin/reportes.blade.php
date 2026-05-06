@extends('admin.layout')
@section('title', 'Reportes')

@section('content')

<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Reportes</h1>
    <p class="text-gray-400 text-sm mt-1">Gestiona todos los reportes de la plataforma</p>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- FILTROS --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.reportes') }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ !request('tipo') ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
        Todos
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ !request('tipo') ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalTodos }}
        </span>
    </a>
    <a href="{{ route('admin.reportes', ['tipo' => 'resena']) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ request('tipo') === 'resena' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-500 border-gray-200 hover:border-yellow-300' }}">
        <i class="bi bi-star mr-1"></i> Reseñas
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ request('tipo') === 'resena' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalResenas }}
        </span>
    </a>
    <a href="{{ route('admin.reportes', ['tipo' => 'servicio']) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ request('tipo') === 'servicio' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-500 border-gray-200 hover:border-blue-300' }}">
        <i class="bi bi-scissors mr-1"></i> Servicios
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ request('tipo') === 'servicio' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalServicios }}
        </span>
    </a>
    <a href="{{ route('admin.reportes', ['tipo' => 'profesional']) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ request('tipo') === 'profesional' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-500 border-gray-200 hover:border-red-300' }}">
        <i class="bi bi-person-x mr-1"></i> Profesionales
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ request('tipo') === 'profesional' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalProfesionales }}
        </span>
    </a>
    <a href="{{ route('admin.reportes', ['tipo' => 'disputa']) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ request('tipo') === 'disputa' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-500 border-gray-200 hover:border-orange-300' }}">
        <i class="bi bi-exclamation-triangle mr-1"></i> Disputas
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ request('tipo') === 'disputa' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalDisputas ?? 0 }}
        </span>
    </a>
</div>

{{-- LISTA DE REPORTES --}}
@forelse($reportes as $reporte)

    {{-- REPORTE DE RESEÑA --}}
    @if($reporte['tipo'] === 'resena')
    <div id="item-reporte-{{ $reporte['data']->id }}" class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6 mb-4 shadow-sm transition">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-yellow-50 text-yellow-600 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1">
                        <i class="bi bi-star-fill text-xs"></i> Reseña
                    </span>
                    @if($reporte['data']->estado === 'pendiente')
                        <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-full font-medium">Pendiente</span>
                    @else
                        <span class="bg-gray-100 text-gray-400 text-xs px-2.5 py-1 rounded-full font-medium">Revisado</span>
                    @endif
                    <span class="text-xs text-gray-300">{{ $reporte['data']->created_at->diffForHumans() }}</span>
                </div>

                <p class="text-sm font-semibold text-gray-700 mb-2">
                    Reseña de <span class="text-blue-600">{{ $reporte['data']->resena->cliente->name ?? '—' }}</span>
                    al profesional <span class="text-blue-600">{{ $reporte['data']->resena->profesional->name ?? '—' }}</span>
                </p>

                <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 mb-3 text-sm text-gray-500 italic">
                    "{{ $reporte['data']->resena->comentario ?? 'Reseña eliminada' }}"
                </div>

                <p class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-600">Motivo:</span> {{ $reporte['data']->motivo }}
                </p>
                <p class="text-xs text-gray-300 mt-1">
                    Reportado por {{ $reporte['data']->user->name ?? '—' }}
                </p>
            </div>

            @if($reporte['data']->resena)
            <div class="flex flex-row sm:flex-col gap-2 sm:flex-shrink-0">
                <form method="POST" action="{{ route('admin.reportes.eliminar', $reporte['data']->resena->id) }}"
                    onsubmit="return confirm('¿Eliminar esta reseña?')" class="flex-1 sm:flex-none">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <i class="bi bi-trash mr-1"></i> Eliminar reseña
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.reportes.ignorar', $reporte['data']->id) }}"
                    class="flex-1 sm:flex-none">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full border border-gray-200 hover:bg-gray-50 text-gray-500 text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <i class="bi bi-check2 mr-1"></i> Ignorar
                    </button>
                </form>
            </div>
            @else
            <span class="text-xs text-gray-300 italic self-start">Reseña ya eliminada</span>
            @endif
        </div>
    </div>

    {{-- REPORTE DE SERVICIO --}}
    @elseif($reporte['tipo'] === 'servicio')
    <div id="item-reporte-{{ $reporte['data']->id }}" class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6 mb-4 shadow-sm transition">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-blue-50 text-blue-600 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1">
                        <i class="bi bi-scissors text-xs"></i> Servicio
                    </span>
                    @if($reporte['data']->estado === 'pendiente')
                        <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-full font-medium">Pendiente</span>
                    @else
                        <span class="bg-gray-100 text-gray-400 text-xs px-2.5 py-1 rounded-full font-medium">Revisado</span>
                    @endif
                    <span class="text-xs text-gray-300">{{ $reporte['data']->created_at->diffForHumans() }}</span>
                </div>

                @if($reporte['data']->servicio)
                <p class="text-sm font-semibold text-gray-700 mb-2">
                    Servicio: <span class="text-blue-600">{{ $reporte['data']->servicio->titulo }}</span>
                    de <span class="text-blue-600">{{ $reporte['data']->profesional->name ?? '—' }}</span>
                </p>
                <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 mb-3 text-sm text-gray-500">
                    {{ $reporte['data']->servicio->descripcion }}
                </div>
                @else
                <p class="text-sm text-gray-400 italic mb-2">Servicio eliminado</p>
                @endif

                <p class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-600">Motivo:</span> {{ $reporte['data']->motivo }}
                </p>
                <p class="text-xs text-gray-300 mt-1">
                    Reportado por {{ $reporte['data']->user->name ?? '—' }}
                </p>
            </div>

            <div class="flex flex-row sm:flex-col gap-2 sm:flex-shrink-0">
                @if($reporte['data']->servicio)
                <form method="POST" action="{{ route('admin.reportes.eliminarServicio', $reporte['data']->servicio->id) }}"
                    onsubmit="return confirm('¿Eliminar este servicio?')" class="flex-1 sm:flex-none">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <i class="bi bi-trash mr-1"></i> Eliminar servicio
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.reportes.ignorarServicio', $reporte['data']->id) }}"
                    class="flex-1 sm:flex-none">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full border border-gray-200 hover:bg-gray-50 text-gray-500 text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <i class="bi bi-check2 mr-1"></i> Ignorar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- REPORTE DE PROFESIONAL --}}
    @elseif($reporte['tipo'] === 'profesional')
    <div id="item-reporte-{{ $reporte['data']->id }}" class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6 mb-4 shadow-sm transition">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-red-50 text-red-500 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1">
                        <i class="bi bi-person-x text-xs"></i> Profesional
                    </span>
                    @if($reporte['data']->estado === 'pendiente')
                        <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-full font-medium">Pendiente</span>
                    @else
                        <span class="bg-gray-100 text-gray-400 text-xs px-2.5 py-1 rounded-full font-medium">Revisado</span>
                    @endif
                    <span class="text-xs text-gray-300">{{ $reporte['data']->created_at->diffForHumans() }}</span>
                </div>

                <p class="text-sm font-semibold text-gray-700 mb-2">
                    Profesional reportado:
                    <a href="javascript:void(0)" onclick="verUsuario({{ $reporte['data']->profesional_id }})"
                        class="text-blue-600 hover:underline">
                        {{ $reporte['data']->profesional->name ?? '—' }}
                    </a>
                </p>

                <p class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-600">Motivo:</span> {{ $reporte['data']->motivo }}
                </p>
                <p class="text-xs text-gray-300 mt-1">
                    Reportado por {{ $reporte['data']->user->name ?? '—' }}
                </p>
            </div>

            <div class="flex flex-row sm:flex-col gap-2 sm:flex-shrink-0">
                @if($reporte['data']->profesional)
                <a href="javascript:void(0)" onclick="verUsuario({{ $reporte['data']->profesional_id }})"
                    class="flex-1 sm:flex-none bg-gray-800 hover:bg-gray-900 text-white text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap text-center">
                    <i class="bi bi-person mr-1"></i> Ver usuario
                </a>
                @endif
                <form method="POST" action="{{ route('admin.reportes.ignorarServicio', $reporte['data']->id) }}"
                    class="flex-1 sm:flex-none">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full border border-gray-200 hover:bg-gray-50 text-gray-500 text-xs px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <i class="bi bi-check2 mr-1"></i> Ignorar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- REPORTE DISPUTA --}}
    @elseif($reporte['tipo'] === 'disputa')
    <div id="item-reporte-{{ $reporte['data']->id }}" class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6 mb-4 shadow-sm transition">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-orange-50 text-orange-600 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1">
                        <i class="bi bi-exclamation-triangle text-xs"></i> Disputa de Servicio
                    </span>
                    @if($reporte['data']->estado === 'pendiente')
                        <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-full font-medium">Pendiente</span>
                    @else
                        <span class="bg-green-50 text-green-600 text-xs px-2.5 py-1 rounded-full font-medium">Resuelto</span>
                    @endif
                    <span class="text-xs text-gray-300">{{ $reporte['data']->created_at->diffForHumans() }}</span>
                </div>

                <p class="text-sm font-semibold text-gray-700 mb-2">
                    Servicio <span class="text-blue-600">{{ $reporte['data']->reserva->servicio->titulo ?? '—' }}</span>
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Cliente (Reportó):</p>
                        <p class="text-sm font-semibold flex justify-between items-center">
                            <a href="javascript:void(0)" onclick="verUsuario({{ $reporte['data']->cliente_id }})" class="text-blue-600 hover:underline">{{ $reporte['data']->cliente->name }}</a>
                            @if(!$reporte['data']->cliente->activo)
                                <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Deshabilitado</span>
                            @endif
                        </p>
                        @php
                            $disputasCliente = \App\Models\DisputaReserva::where('cliente_id', $reporte['data']->cliente_id)->count();
                        @endphp
                        <p class="text-[10px] text-gray-400 mt-1">Historial: {{ $disputasCliente }} disputas</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Profesional:</p>
                        <p class="text-sm font-semibold flex justify-between items-center">
                            <a href="javascript:void(0)" onclick="verUsuario({{ $reporte['data']->profesional_id }})" class="text-blue-600 hover:underline">{{ $reporte['data']->profesional->name }}</a>
                            @if(!$reporte['data']->profesional->activo)
                                <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Deshabilitado</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 mb-3 text-sm text-red-800">
                    <span class="font-bold">Motivo del problema:</span> <br>
                    "{{ $reporte['data']->motivo }}"
                </div>
            </div>

            <div class="flex flex-col gap-2 w-full sm:w-48 sm:flex-shrink-0">
                @if($reporte['data']->estado === 'pendiente')
                <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
                    <p class="text-xs font-bold text-gray-600 mb-2">Resolver Disputa</p>
                    <form method="POST" action="{{ route('admin.disputas.cliente', $reporte['data']->id) }}" class="mb-2">
                        @csrf
                        <input type="text" name="resolucion_admin" placeholder="Nota de resolución..." class="w-full text-xs border border-gray-200 rounded p-1.5 mb-2 focus:ring-red-300" required>
                        <button type="submit" onclick="return confirm('¿Reembolsar el dinero al cliente?')" class="w-full bg-red-500 hover:bg-red-600 text-white text-[11px] px-3 py-1.5 rounded-lg transition cursor-pointer">
                            Reembolsar a Cliente
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.disputas.profesional', $reporte['data']->id) }}">
                        @csrf
                        <input type="hidden" name="resolucion_admin" value="Pago liberado al profesional">
                        <button type="submit" onclick="return confirm('¿Liberar el dinero al profesional?')" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-[11px] px-3 py-1.5 rounded-lg transition cursor-pointer">
                            Liberar a Profesional
                        </button>
                    </form>
                </div>
                @endif
                
                <div class="flex flex-col gap-1 mt-2">
                    <form method="POST" action="{{ route('admin.usuarios.deshabilitar', $reporte['data']->cliente_id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full text-[10px] text-gray-500 hover:text-red-600 text-left px-2 py-1 transition cursor-pointer">
                            <i class="bi bi-power"></i> {{ $reporte['data']->cliente->activo ? 'Deshabilitar Cliente' : 'Habilitar Cliente' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.usuarios.deshabilitar', $reporte['data']->profesional_id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full text-[10px] text-gray-500 hover:text-red-600 text-left px-2 py-1 transition cursor-pointer">
                            <i class="bi bi-power"></i> {{ $reporte['data']->profesional->activo ? 'Deshabilitar Profesional' : 'Habilitar Profesional' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

@empty
<div class="text-center py-16 text-gray-300">
    <i class="bi bi-flag text-5xl mb-4 block"></i>
    <p class="font-medium text-gray-400">No hay reportes
        @if(request('tipo') === 'resena') de reseñas
        @elseif(request('tipo') === 'servicio') de servicios
        @elseif(request('tipo') === 'profesional') de profesionales
        @endif
        pendientes
    </p>
</div>
@endforelse

<div class="mt-6 flex justify-end">
    {{ $reportes->links() }}
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash;
    if (hash) {
        const el = document.querySelector(hash);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('bg-blue-50');
            setTimeout(() => el.classList.remove('bg-blue-50'), 3000);
        }
    }
});
</script>
@endpush

@include('admin.partials.usuario_drawer')

@endsection