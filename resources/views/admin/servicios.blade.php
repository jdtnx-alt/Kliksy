@extends('admin.layout')
@section('title', 'Servicios')

@section('content')

<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Servicios</h1>
    <p class="text-gray-400 text-sm mt-1">Gestiona los servicios publicados en la plataforma</p>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">#</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Título</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Categoría</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Precio</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Profesional</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Fecha</th>
                    <th class="text-left px-4 sm:px-6 py-4 font-semibold text-gray-400 text-xs uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($servicios as $servicio)
                <tr id="item-servicio-{{ $servicio->id }}" class="hover:bg-gray-50 transition">
                    <td class="px-4 sm:px-6 py-4 text-gray-300">{{ $servicio->id }}</td>
                    <td class="px-4 sm:px-6 py-4 font-medium text-gray-800">
                        <span class="block max-w-[160px] truncate">{{ $servicio->titulo }}</span>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="bg-gray-100 text-gray-500 px-2 sm:px-3 py-1 rounded-full text-xs whitespace-nowrap">
                            {{ ucfirst($servicio->categoria) }}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-blue-600 font-semibold whitespace-nowrap">
                        ${{ number_format($servicio->precio, 0, ',', '.') }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-gray-400 text-xs sm:text-sm">
                        <span class="block max-w-[120px] truncate">{{ $servicio->user->name ?? '—' }}</span>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                        {{ $servicio->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <form method="POST" action="{{ route('admin.servicios.eliminar', $servicio->id) }}"
                            onsubmit="return confirm('¿Eliminar este servicio?')">
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
    {{ $servicios->links() }}
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

@endsection