<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Kliksy Pro</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; overflow-x: hidden; }

        /* SIDEBAR DESKTOP */
        .pro-sidebar { transition: width 0.3s ease; width: 240px; }
        .pro-sidebar.collapsed { width: 68px; }
        .pro-sidebar.collapsed .nav-label,
        .pro-sidebar.collapsed .brand-text,
        .pro-sidebar.collapsed .user-info,
        .pro-sidebar.collapsed .vac-text { display: none; }
        .pro-sidebar.collapsed .sb-header { justify-content: center; }
        .pro-sidebar.collapsed .sb-user { justify-content: center; }

        .pro-main { transition: margin-left 0.3s ease; margin-left: 240px; width: calc(100% - 240px); }
        .pro-main.collapsed { margin-left: 68px; width: calc(100% - 68px); }

        /* SIDEBAR MÓVIL */
        @media (max-width: 767px) {
            .pro-sidebar {
                width: 240px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 200;
            }
            .pro-sidebar.mobile-open { transform: translateX(0); }
            .pro-main { margin-left: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body class="bg-gray-50">

<script>
    if (localStorage.getItem('proSidebarCollapsed') === 'true') {
        document.addEventListener('DOMContentLoaded', () => {
            const s = document.getElementById('proSidebar');
            const m = document.getElementById('proMain');
            if (window.innerWidth >= 768) {
                s?.classList.add('collapsed');
                m?.classList.add('collapsed');
            }
        });
    }
</script>

{{-- OVERLAY MÓVIL --}}
<div id="proSidebarOverlay"
    onclick="closeMobileSidebar()"
    class="fixed inset-0 bg-gray-900/45 z-[150] hidden md:hidden cursor-pointer">
</div>

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="proSidebar" class="pro-sidebar bg-white border-r border-gray-200 flex flex-col py-6 px-3 fixed top-0 left-0 bottom-0 shadow-sm">

        {{-- HEADER --}}
        <div class="sb-header flex items-center justify-between mb-8 px-2">
            <div class="brand-text flex items-center gap-2 overflow-hidden">
                <div class="bg-blue-600 text-white w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">K</div>
                <div>
                    <p class="font-bold text-gray-800 text-sm leading-tight whitespace-nowrap">Kliksy</p>
                    <p class="text-xs text-gray-400 whitespace-nowrap">Panel profesional</p>
                </div>
            </div>
            <button onclick="handleSidebarToggle()"
                class="text-gray-400 hover:text-gray-600 transition flex-shrink-0 cursor-pointer w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="bi bi-layout-sidebar text-base"></i>
            </button>
        </div>

        {{-- NAV --}}
        <nav class="flex flex-col gap-0.5 flex-1">
            <a href="{{ route('profesional.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-grid-1x2 text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Dashboard</span>
            </a>
            <a href="{{ route('profesional.perfil') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.perfil') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-person text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Mi Perfil</span>
            </a>
            <a href="{{ route('profesional.servicios.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.servicios.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-scissors text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Servicios</span>
                @php $totalServicios = \App\Models\Servicio::where('user_id', auth()->id())->count(); @endphp
                @if($totalServicios > 0)
                <span class="ml-auto nav-label bg-blue-50 text-blue-600 text-xs px-2 py-0.5 rounded-full flex-shrink-0 {{ request()->routeIs('profesional.servicios.index') ? 'bg-white/20 text-white' : '' }}">{{ $totalServicios }}</span>
                @endif
            </a>
            <a href="{{ route('profesional.resenas') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.resenas') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-star text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Reseñas</span>
                @php $totalResenas = auth()->user()->resenas()->count(); @endphp
                @if($totalResenas > 0)
                <span class="ml-auto nav-label bg-yellow-50 text-yellow-600 text-xs px-2 py-0.5 rounded-full flex-shrink-0 {{ request()->routeIs('profesional.resenas') ? 'bg-white/20 text-white' : '' }}">{{ $totalResenas }}</span>
                @endif
            </a>
            <a href="{{ route('profesional.reservas') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.reservas') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
    <i class="bi bi-calendar-check text-base flex-shrink-0"></i>
    <span class="nav-label text-sm font-medium">Reservas</span>
    @php $reservasPendientes = \App\Models\Reserva::where('profesional_id', auth()->id())->where('estado','pendiente')->count(); @endphp
    @if($reservasPendientes > 0)
    <span class="ml-auto nav-label bg-yellow-50 text-yellow-600 text-xs px-2 py-0.5 rounded-full flex-shrink-0 {{ request()->routeIs('profesional.reservas') ? 'bg-white/20 text-white' : '' }}">{{ $reservasPendientes }}</span>
    @endif
</a>
            <a href="{{ route('profesional.negocio') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('profesional.negocio') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-shop text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Negocio</span>
            </a>
        </nav>

        {{-- FOOTER --}}
        <div class="border-t border-gray-200 pt-4 mt-4">
            {{-- USUARIO --}}
            <div class="sb-user flex items-center gap-2 px-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="user-info overflow-hidden">
                    <p class="text-sm font-semibold text-gray-800 truncate leading-tight">{{ auth()->user()->name }}</p>
                    @php $enVacaciones = auth()->user()->perfilProfesional?->en_vacaciones ?? false; @endphp
                    <p class="text-xs {{ $enVacaciones ? 'text-orange-500' : 'text-green-500' }}">
                        {{ $enVacaciones ? '● En vacaciones' : '● Visible' }}
                    </p>
                </div>
            </div>

            {{-- MODO VACACIONES --}}
            <form method="POST" action="{{ route('profesional.vacaciones') }}" class="mb-2">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-xl transition cursor-pointer
                    {{ $enVacaciones ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                    <i class="bi {{ $enVacaciones ? 'bi-moon-fill' : 'bi-moon' }} text-base flex-shrink-0"></i>
                    <span class="nav-label vac-text text-sm font-medium">
                        {{ $enVacaciones ? 'En vacaciones' : 'Modo vacaciones' }}
                    </span>
                </button>
            </form>

            {{-- CERRAR SESIÓN --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-500 transition cursor-pointer">
                    <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                    <span class="nav-label text-sm font-medium">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div id="proMain" class="pro-main flex-1 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-4 sm:px-8 py-3 flex items-center gap-3 sticky top-0 z-40">

            {{-- HAMBURGUESA MÓVIL --}}
            <button onclick="openMobileSidebar()"
                class="md:hidden text-gray-500 hover:text-gray-700 w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition flex-shrink-0 cursor-pointer">
                <i class="bi bi-list text-xl"></i>
            </button>

            {{-- TÍTULO DE PÁGINA --}}
            <div class="flex-1">
                <h1 class="text-sm font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>

            {{-- DERECHA --}}
<div class="flex items-center gap-2">
                <a href="{{ route('inicio') }}" target="_blank"
                    class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded-xl transition">
                    <i class="bi bi-box-arrow-up-right text-xs"></i>
                    Ver plataforma
                </a>
                <a href="{{ route('profesional.publico', auth()->id()) }}" target="_blank"
                    class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-3 py-1.5 rounded-xl transition">
                    <i class="bi bi-person-badge text-xs"></i>
                    Mi perfil público
                </a>
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        {{-- ALERTS --}}
        @if(session('success'))
        <div class="mx-4 sm:mx-8 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 sm:mx-8 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i> {{ session('error') }}
        </div>
        @endif

        {{-- CONTENIDO --}}
        <main class="flex-1 p-4 sm:p-8">
            @if(!auth()->user()->hasVerifiedEmail())
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-envelope-exclamation text-yellow-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-yellow-800 text-sm">Verifica tu correo electrónico</p>
                        <p class="text-yellow-600 text-xs mt-0.5">Necesitas verificar tu correo para acceder a todas las funciones</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('verification.send') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap text-center transition cursor-pointer">
                        Reenviar verificación
                    </button>
                </form>
            </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<script>
function toggleSidebar() {
    const s = document.getElementById('proSidebar');
    const m = document.getElementById('proMain');
    s.classList.toggle('collapsed');
    m.classList.toggle('collapsed');
    localStorage.setItem('proSidebarCollapsed', s.classList.contains('collapsed'));
}
function openMobileSidebar() {
    document.getElementById('proSidebar').classList.add('mobile-open');
    document.getElementById('proSidebarOverlay').classList.remove('hidden');
}
function closeMobileSidebar() {
    document.getElementById('proSidebar').classList.remove('mobile-open');
    document.getElementById('proSidebarOverlay').classList.add('hidden');
}
function handleSidebarToggle() {
    if (window.innerWidth < 768) closeMobileSidebar();
    else toggleSidebar();
}
</script>

@stack('scripts')
</body>
</html>