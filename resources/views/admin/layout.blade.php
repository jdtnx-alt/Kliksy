<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Kliksy</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; overflow-x: hidden; }

        /* SIDEBAR DESKTOP */
        .sidebar { transition: width 0.3s ease; width: 240px; }
        .sidebar.collapsed { width: 68px; }
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .brand-text { display: none; }
        .sidebar.collapsed > div:first-child { justify-content: center; }

        .main-content { transition: margin-left 0.3s ease; margin-left: 240px; width: calc(100% - 240px); }
        .main-content.collapsed { margin-left: 68px; width: calc(100% - 68px); }

        /* SIDEBAR MÓVIL */
        @media (max-width: 767px) {
            .sidebar {
                width: 240px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 200;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

<script>
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.addEventListener('DOMContentLoaded', () => {
            const s = document.getElementById('sidebar');
            const m = document.getElementById('mainContent');
            if (window.innerWidth >= 768) {
                s?.classList.add('collapsed');
                m?.classList.add('collapsed');
            }
        });
    }
</script>

{{-- OVERLAY MÓVIL SIDEBAR --}}
<div id="sidebarOverlay"
    onclick="closeMobileSidebar()"
    class="fixed inset-0 bg-gray-900/45 z-[150] hidden md:hidden cursor-pointer">
</div>

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar bg-white border-r border-gray-300 flex flex-col py-6 px-3 fixed top-0 left-0 bottom-0 shadow-sm">

        <div class="flex items-center justify-between mb-8 px-2">
            <div class="brand-text flex items-center gap-2 overflow-hidden">
                <div class="bg-blue-600 text-white w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">K</div>
                <div>
                    <p class="font-bold text-gray-800 text-sm leading-tight whitespace-nowrap">Kliksy</p>
                    <p class="text-xs text-gray-400 whitespace-nowrap">Management Suite</p>
                </div>
            </div>
            {{-- Botón toggle — en desktop colapsa, en móvil cierra --}}
            <button onclick="handleSidebarToggle()"
                class="text-gray-400 hover:text-gray-600 transition flex-shrink-0 cursor-pointer w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="bi bi-layout-sidebar text-base"></i>
            </button>
        </div>

        <nav class="flex flex-col gap-0.5 flex-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-grid-1x2 text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.usuarios') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.usuarios') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-people text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Usuarios</span>
            </a>
            <a href="{{ route('admin.servicios') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.servicios') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-scissors text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Servicios</span>
            </a>
            <a href="{{ route('admin.reportes') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reportes') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <i class="bi bi-flag text-base flex-shrink-0"></i>
                <span class="nav-label text-sm font-medium">Reportes</span>
                @php $pendientesCount = \App\Models\Reporte::where('estado','pendiente')->count(); @endphp
                @if($pendientesCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center nav-label flex-shrink-0">{{ $pendientesCount }}</span>
                @endif
            </a>
        </nav>

        <div class="border-t border-gray-300 pt-4 mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-500 transition cursor-pointer">
                    <i class="bi bi-box-arrow-right text-base flex-shrink-0"></i>
                    <span class="nav-label text-sm font-medium">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div id="mainContent" class="main-content flex-1 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-4 sm:px-8 py-3 flex items-center gap-3 sm:gap-4 sticky top-0 z-40">

            {{-- BOTÓN HAMBURGUESA — solo móvil --}}
            <button onclick="openMobileSidebar()"
                class="md:hidden text-gray-500 hover:text-gray-700 w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition flex-shrink-0 cursor-pointer">
                <i class="bi bi-list text-xl"></i>
            </button>

            {{-- BUSCADOR --}}
            <div class="relative flex-1 max-w-xs sm:max-w-md">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" id="adminSearch"
                    placeholder="Buscar..."
                    autocomplete="off"
                    class="w-full bg-gray-50 border border-gray-300 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition">
                <div id="searchDropdown"
                    class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden max-h-80 overflow-y-auto">
                </div>
            </div>

            {{-- DERECHA --}}
            <div class="ml-auto flex items-center gap-3 flex-shrink-0">
                <button onclick="openPerfilAdmin()"
                    class="flex items-center gap-2 sm:gap-3 pl-3 border-l border-gray-200 cursor-pointer hover:opacity-80 transition">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Super Admin</p>
                    </div>
                </button>
            </div>
        </header>

        {{-- CONTENIDO --}}
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>

    </div>
</div>

<script>
// DESKTOP: colapsa sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    sidebar.classList.toggle('collapsed');
    main.classList.toggle('collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

// MÓVIL: abre sidebar con overlay
function openMobileSidebar() {
    document.getElementById('sidebar').classList.add('mobile-open');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
}
function closeMobileSidebar() {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}

// Detecta si estamos en móvil o desktop para el botón del sidebar
function handleSidebarToggle() {
    if (window.innerWidth < 768) {
        closeMobileSidebar();
    } else {
        toggleSidebar();
    }
}

// BUSCADOR
const searchInput = document.getElementById('adminSearch');
const searchDropdown = document.getElementById('searchDropdown');
let searchTimer;

const iconos = {
    usuario: '<i class="bi bi-person text-blue-500"></i>',
    servicio: '<i class="bi bi-scissors text-green-500"></i>',
    reporte: '<i class="bi bi-flag text-red-500"></i>'
};
const badgeColors = {
    usuario: 'bg-blue-50 text-blue-600',
    servicio: 'bg-green-50 text-green-600',
    reporte: 'bg-red-50 text-red-500'
};

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { searchDropdown.classList.add('hidden'); return; }
    searchTimer = setTimeout(() => {
        fetch(`{{ route('admin.buscar') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.resultados || data.resultados.length === 0) {
                    searchDropdown.innerHTML = `<div class="px-4 py-3 text-sm text-gray-400">Sin resultados para "${q}"</div>`;
                    searchDropdown.classList.remove('hidden');
                    return;
                }
                searchDropdown.innerHTML = data.resultados.map(r => `
                    <a href="${r.url}#item-${r.tipo}-${r.id}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition cursor-pointer border-b border-gray-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">${iconos[r.tipo]}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">${r.label}</p>
                            <p class="text-xs text-gray-400 truncate">${r.sub}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 ${badgeColors[r.tipo]}">${r.badge}</span>
                    </a>
                `).join('');
                searchDropdown.classList.remove('hidden');
            });
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
        searchDropdown.classList.add('hidden');
    }
});
</script>

{{-- OVERLAY PERFIL ADMIN --}}
<div id="perfilAdminOverlay"
    onclick="closePerfilAdmin()"
    class="fixed inset-0 bg-gray-900/45 z-[100] hidden cursor-pointer">
</div>

{{-- DRAWER PERFIL ADMIN --}}
<aside id="perfilAdminDrawer"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl"
    style="transform: translateX(100%); transition: transform 0.55s cubic-bezier(.77,0,.18,1);">

    <div class="relative bg-blue-600 px-9 pt-8 pb-7 flex-shrink-0 overflow-hidden">
        <div style="position:absolute;width:280px;height:280px;border-radius:50%;border:40px solid rgba(255,255,255,0.08);top:-80px;right:-80px;pointer-events:none;"></div>
        <button onclick="closePerfilAdmin()"
            class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">
            ✕
        </button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy Admin</span>
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-bold text-xl">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl text-white font-bold leading-tight">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-white/70">{{ auth()->user()->email }}</p>
                <span class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-full mt-1 inline-block">Super Admin</span>
            </div>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-9 pt-7 pb-8">

        @if(session('perfil_success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">
            {{ session('perfil_success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.perfil') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Nombre</label>
                <input type="text" name="name" required
                    value="{{ old('name', auth()->user()->name) }}"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required
                    value="{{ old('email', auth()->user()->email) }}"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Teléfono</label>
                <input type="text" name="telefono"
                    value="{{ old('telefono', auth()->user()->telefono) }}"
                    placeholder="Sin teléfono"
                    class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
            </div>
            <div class="border-t border-slate-100 pt-4 mt-4 mb-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Cambiar contraseña</p>
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1.5">Nueva contraseña</label>
                    <input type="password" name="password"
                        placeholder="Dejar vacío para no cambiar"
                        class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
                </div>
                <div class="mb-6">
                    <label class="block text-xs text-slate-400 mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                        placeholder="Repite la nueva contraseña"
                        class="w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-blue-500 focus:bg-white outline-none transition">
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold transition shadow-lg shadow-blue-500/30">
                Guardar cambios
            </button>
        </form>
    </div>
</aside>

<script>
function openPerfilAdmin() {
    document.getElementById('perfilAdminDrawer').style.transform = 'translateX(0)';
    document.getElementById('perfilAdminOverlay').classList.remove('hidden');
}
function closePerfilAdmin() {
    document.getElementById('perfilAdminDrawer').style.transform = 'translateX(100%)';
    document.getElementById('perfilAdminOverlay').classList.add('hidden');
}

@if(session('perfil_success'))
    document.addEventListener('DOMContentLoaded', () => openPerfilAdmin());
@endif
</script>

@stack('scripts')
</body>
</html>