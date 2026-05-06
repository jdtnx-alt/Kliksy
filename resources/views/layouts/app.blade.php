<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Kliksy — Servicios a domicilio en Florencia, Caquetá')</title>
<meta name="description" content="@yield('descripcion', 'Encuentra profesionales verificados de barbería, plomería, electricidad, belleza y más servicios a domicilio en Florencia, Caquetá.')">
<meta name="robots" content="index, follow">
<meta property="og:title" content="@yield('titulo', 'Kliksy — Servicios a domicilio')">
<meta property="og:description" content="@yield('descripcion', 'Encuentra profesionales verificados cerca de ti.')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }

        @media (min-width: 640px) {
            .page-wrap.drawer-open { transform: translateX(-420px); }
        }

        .login-drawer { transform: translateX(100%); transition: transform 0.55s cubic-bezier(.77,0,.18,1); }
        .login-drawer.open { transform: translateX(0); }
        .reg-drawer { transform: translateX(100%); transition: transform 0.55s cubic-bezier(.77,0,.18,1); }
        .reg-drawer.open { transform: translateX(0); }

        .drawer-top::before {
            content:''; position:absolute; width:280px; height:280px; border-radius:50%;
            border:40px solid rgba(255,255,255,0.08); top:-80px; right:-80px; pointer-events:none;
        }
        .drawer-top::after {
            content:''; position:absolute; width:160px; height:160px; border-radius:50%;
            border:28px solid rgba(255,255,255,0.06); bottom:-40px; left:-40px; pointer-events:none;
        }
        .reg-top::before {
            content:''; position:absolute; width:280px; height:280px; border-radius:50%;
            border:40px solid rgba(255,255,255,0.08); top:-80px; right:-80px; pointer-events:none;
        }
        .reg-top::after {
            content:''; position:absolute; width:160px; height:160px; border-radius:50%;
            border:28px solid rgba(255,255,255,0.06); bottom:-40px; left:-40px; pointer-events:none;
        }

        .kliksy-input:focus {
            border-color: #2563eb !important;
            background: white !important;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.08);
            outline: none;
        }

        .step-enter { animation: stepIn 0.3s cubic-bezier(.22,1,.36,1) both; }
        @keyframes stepIn {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .reg-top { transition: background-color 0.4s ease; }
    </style>
</head>
<body class="bg-gray-100">

<div id="drawerOverlay"
     onclick="closeAll()"
     class="fixed inset-0 bg-gray-900/45 z-[100] hidden cursor-pointer">
</div>

<div class="page-wrap" id="pageWrap">

    {{-- ══════════ NAVBAR ══════════ --}}
    @if(!request()->routeIs('profesional.onboarding', 'auth.google.rol'))
    <nav class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center h-16 gap-4">

            {{-- LOGO --}}
            <a href="{{ route('inicio') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-base">K</div>
                <span class="font-bold text-lg text-gray-800">Kliksy</span>
            </a>

            {{-- LINKS DESKTOP --}}
            <div class="hidden md:flex items-center gap-6 ml-8">
                <a href="{{ route('inicio') }}"
                   class="text-sm font-medium transition {{ request()->is('/') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    Inicio
                </a>
                <a href="/servicios"
                   class="text-sm font-medium transition {{ request()->is('servicios') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    Servicios
                </a>
            </div>

            {{-- DERECHA --}}
            <div class="ml-auto flex items-center gap-1 sm:gap-2">

                @auth

                {{-- CLIENTE: historial --}}
                @if(auth()->user()->role_id === 1)
                @php
                    $historialCliente = \App\Models\Reserva::where('cliente_id', auth()->id())
                        ->where('estado', 'completada')
                        ->where('confirmacion_cliente', 'confirmado')
                        ->with(['servicio', 'profesional'])
                        ->orderBy('updated_at', 'desc')->get();
                    $resenasHechas = \App\Models\Resena::where('cliente_id', auth()->id())
                        ->pluck('servicio_id')->toArray();
                @endphp
                <a href="{{ route('reservas.mis') }}"
    class="relative w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition text-gray-500 hover:text-blue-600"
    title="Mis reservas">
    <i class="bi bi-calendar-check text-base"></i>
</a>
                @endif

                {{-- DROPDOWN USUARIO --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 pl-2 pr-2 sm:pr-3 py-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="hidden sm:block font-medium text-gray-800 text-sm max-w-[100px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="hidden sm:block bi bi-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-lg z-50 py-1.5 overflow-hidden"
                         style="display:none;">

                        {{-- INFO USUARIO --}}
                        <div class="px-4 py-2.5 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        @if(!request()->routeIs('profesional.onboarding', 'auth.google.rol'))
<a href="{{ route('perfil.index') }}"
   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
    <i class="bi bi-person text-gray-400 w-4"></i> Mi Perfil
</a>
@if(auth()->user()->role_id === 2)
<a href="{{ route('profesional.dashboard') }}"
    class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
    <i class="bi bi-columns-gap text-gray-400 w-4"></i> Dashboard profesional
</a>
@endif
@else
<div class="px-4 py-2.5 flex items-center gap-2 text-xs text-gray-400">
    <i class="bi bi-lock text-gray-300"></i>
    Completa tu registro primero
</div>
@endif

                        {{-- LINKS MÓVIL --}}
                        <div class="md:hidden border-t border-gray-100 mt-1 pt-1">
                            <a href="{{ route('inicio') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-gray-50 transition {{ request()->is('/') ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
                                <i class="bi bi-house text-gray-400 w-4"></i> Inicio
                            </a>
                            <a href="/servicios"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-gray-50 transition {{ request()->is('servicios') ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
                                <i class="bi bi-grid text-gray-400 w-4"></i> Servicios
                            </a>
                        </div>

                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition cursor-pointer">
                                    <i class="bi bi-box-arrow-right w-4"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @else

                {{-- GUEST --}}
                <button onclick="openLogin()"
                    class="h-9 flex items-center px-3 sm:px-4 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl transition cursor-pointer">
                    Iniciar sesión
                </button>
                <button onclick="openRegister()"
                    class="h-9 flex items-center bg-blue-600 text-white px-3 sm:px-4 rounded-xl hover:bg-blue-700 transition cursor-pointer text-sm font-medium shadow-sm shadow-blue-200">
                    <span class="hidden sm:inline">Registrarse</span>
                    <span class="sm:hidden">Registro</span>
                </button>

                @endauth

            </div>
        </div>

        {{-- BARRA MÓVIL GUEST --}}
        @guest
        <div class="md:hidden border-t border-gray-100 px-4 py-2 flex gap-5">
            <a href="{{ route('inicio') }}"
               class="text-sm font-medium {{ request()->is('/') ? 'text-blue-600' : 'text-gray-500' }}">
                Inicio
            </a>
            <a href="/servicios"
               class="text-sm font-medium {{ request()->is('servicios') ? 'text-blue-600' : 'text-gray-500' }}">
                Servicios
            </a>
        </div>
        @endguest
    </nav>
    @else
<nav class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center h-16">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center font-black text-base">K</div>
            <span class="font-bold text-lg text-gray-800">Kliksy</span>
        </div>
        <div class="ml-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-red-500 transition cursor-pointer flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </button>
            </form>
        </div>
    </div>
</nav>
@endif

    <main>
    @auth
    @if(!auth()->user()->hasVerifiedEmail())
<div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-2 text-yellow-800 text-sm">
            <i class="bi bi-exclamation-circle-fill text-yellow-500 flex-shrink-0"></i>
            <span>Verifica tu correo electrónico para acceder a todas las funciones.</span>
        </div>
        <form method="POST" action="{{ route('verification.send') }}" class="flex-shrink-0" id="resendVerifyForm">
            @csrf
            <button type="submit"
                class="text-xs font-semibold text-yellow-700 hover:text-yellow-900 underline cursor-pointer bg-transparent border-none transition">
                Enviar verificacion
            </button>
        </form>
    </div>
</div>
@endif
    @endauth
    @yield('content')
</main>

    <x-footer />
    @stack('scripts')

</div>

{{-- ══════════════════════════════════
     DRAWER LOGIN
══════════════════════════════════ --}}
@guest
<aside id="loginDrawer"
       class="login-drawer fixed top-0 right-0 bottom-0 w-full sm:w-[420px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl">

    <div class="drawer-top relative bg-blue-600 px-8 pt-8 pb-7 flex-shrink-0 overflow-hidden">
        <button onclick="closeAll()"
                class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">✕</button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy</span>
        </div>
        <div class="relative z-10">
            <h2 class="text-3xl text-white font-bold leading-tight tracking-tight mb-1.5">Bienvenido<br>de vuelta</h2>
            <p class="text-sm text-white/70">Ingresa tus credenciales para continuar</p>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-8 pt-7 pb-8 flex flex-col">

        @if ($errors->hasAny(['email', 'password']) && !$errors->hasAny(['name', 'telefono', 'role']))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2">
            <i class="bi bi-exclamation-circle flex-shrink-0"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="tu@correo.com"
                       class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Contraseña</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••••" id="drawerPw"
                           class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 pr-11 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200">
                    <button type="button" onclick="togglePw()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg id="eyeOpen" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-200">
                Iniciar sesión
            </button>
            <div class="relative my-5">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-slate-200"></div>
    </div>
    <div class="relative flex justify-center text-xs">
        <span class="bg-white px-3 text-slate-400">o continúa con</span>
    </div>
</div>

<a href="{{ route('auth.google') }}"
    class="w-full flex items-center justify-center gap-3 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium py-3 rounded-xl transition-all duration-200">
    <svg width="18" height="18" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
    </svg>
    Continuar con Google
</a>
            <div class="text-center mt-3">
                <button type="button"
                    onclick="const emailVal = document.getElementById('drawerPw') ? document.querySelector('#loginDrawer input[type=email]').value : '';
document.getElementById('loginDrawer').classList.remove('open');
document.getElementById('forgotDrawer').classList.add('open');
document.querySelector('#forgotDrawer input[type=email]').value = emailVal;"
                    class="text-sm text-slate-400 hover:text-blue-600 cursor-pointer bg-transparent border-none transition">
                    ¿Olvidaste tu contraseña?
                </button>
            </div>
        </form>

        <div class="mt-auto pt-5 border-t border-slate-100 text-center text-sm text-slate-500">
            ¿Sin cuenta aún?
            <button onclick="closeAll(); openRegister()"
                    class="text-blue-600 font-bold hover:underline ml-1 bg-transparent border-none cursor-pointer">
                Regístrate gratis
            </button>
        </div>
    </div>
</aside>

{{-- DRAWER FORGOT --}}
<aside id="forgotDrawer"
       class="login-drawer fixed top-0 right-0 bottom-0 w-full sm:w-[420px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl">

    <div class="drawer-top relative bg-blue-600 px-8 pt-8 pb-7 flex-shrink-0 overflow-hidden">
        <button onclick="document.getElementById('forgotDrawer').classList.remove('open'); document.getElementById('loginDrawer').classList.add('open');"
                class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">✕</button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy</span>
        </div>
        <div class="relative z-10">
            <h2 class="text-3xl text-white font-bold leading-tight tracking-tight mb-1.5">¿Olvidaste<br>tu contraseña?</h2>
            <p class="text-sm text-white/70">Te enviamos un enlace de recuperación</p>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-8 pt-7 pb-8 flex flex-col">

        @if(session('status'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2">
    <i class="bi bi-check-circle flex-shrink-0"></i> {{ session('status') }}
</div>
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="hidden" name="email" value="{{ old('email') }}">
    <p class="text-center text-sm text-slate-400 mb-5">
        ¿No te llegó?
        <button type="submit" class="text-blue-600 hover:underline bg-transparent border-none cursor-pointer font-medium">
            Reenviar correo
        </button>
    </p>
</form>
@endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" required placeholder="tu@correo.com"
                    class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-200">
                Enviar enlace
            </button>
        </form>

        <div class="mt-auto pt-5 border-t border-slate-100 text-center text-sm">
            <button onclick="document.getElementById('forgotDrawer').classList.remove('open'); document.getElementById('loginDrawer').classList.add('open');"
                    class="text-blue-600 font-bold hover:underline bg-transparent border-none cursor-pointer">
                ← Volver al inicio de sesión
            </button>
        </div>
    </div>
</aside>

{{-- DRAWER REGISTRO --}}
<aside id="regDrawer"
       class="reg-drawer fixed top-0 right-0 bottom-0 w-full sm:w-[420px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl">

    <div class="reg-top relative bg-blue-600 px-8 pt-8 pb-7 flex-shrink-0 overflow-hidden" id="regTop">
        <button onclick="closeAll()"
                class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">✕</button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy</span>
        </div>
        <div class="relative z-10 flex items-center gap-2 mb-4" id="stepDots"></div>
        <div class="relative z-10">
            <h2 class="text-2xl text-white font-bold leading-tight tracking-tight mb-1" id="regTitle">Tu perfil</h2>
            <p class="text-sm text-white/70" id="regSubtitle">¿Cómo te llamas?</p>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-8 pt-7 pb-8 flex flex-col">

        @if ($errors->hasAny(['name', 'telefono', 'role']))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
            <i class="bi bi-exclamation-circle flex-shrink-0"></i> {{ $errors->first() }}
        </div>
        @endif

        <div id="stepContent" class="flex-1"></div>

        <div class="pt-4 border-t border-slate-100 mt-4">
    <div class="flex gap-3" id="stepNav"></div>
    <p class="text-center text-xs text-slate-400 mt-3" id="stepFooter"></p>
</div>

<div class="relative my-4">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-slate-200"></div>
    </div>
    <div class="relative flex justify-center text-xs">
        <span class="bg-white px-3 text-slate-400">o regístrate con</span>
    </div>
</div>

<a href="{{ route('auth.google') }}"
    class="w-full flex items-center justify-center gap-3 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium py-3 rounded-xl transition-all duration-200">
    <svg width="18" height="18" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
    </svg>
    Continuar con Google
</a>
    </div>
</aside>

<form id="regForm" method="POST" action="{{ route('register.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="name"                  id="hf_name">
    <input type="hidden" name="email"                 id="hf_email">
    <input type="hidden" name="password"              id="hf_password">
    <input type="hidden" name="password_confirmation" id="hf_confirm">
    <input type="hidden" name="telefono"              id="hf_telefono">
    <input type="hidden" name="role"                  id="hf_role">
</form>
@endguest

<script>
function openLogin() {
    document.getElementById('pageWrap').classList.add('drawer-open');
    document.getElementById('loginDrawer').classList.add('open');
    document.getElementById('drawerOverlay').classList.remove('hidden');
}
function openRegister() {
    document.getElementById('pageWrap').classList.add('drawer-open');
    document.getElementById('regDrawer').classList.add('open');
    document.getElementById('drawerOverlay').classList.remove('hidden');
    currentStep = 1;
    formData = { name:'', email:'', password:'', confirm:'', telefono:'', role:'' };
    renderStep();
}
function closeAll() {
    document.getElementById('pageWrap').classList.remove('drawer-open');
    document.getElementById('loginDrawer').classList.remove('open');
    document.getElementById('regDrawer').classList.remove('open');
    document.getElementById('forgotDrawer').classList.remove('open');
    document.getElementById('drawerOverlay').classList.add('hidden');
}

@if($errors->hasAny(['name', 'telefono', 'role']))
    document.addEventListener('DOMContentLoaded', () => openRegister());
@elseif($errors->hasAny(['email', 'password']) && !old('name'))
    document.addEventListener('DOMContentLoaded', () => openLogin());
@elseif($errors->hasAny(['email', 'password']) && old('name'))
    document.addEventListener('DOMContentLoaded', () => openRegister());
@endif
@if(session('openLogin'))
    document.addEventListener('DOMContentLoaded', () => openLogin());
@endif
@if(session('status'))
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('pageWrap').classList.add('drawer-open');
    document.getElementById('forgotDrawer').classList.add('open');
    document.getElementById('drawerOverlay').classList.remove('hidden');
});
@endif

function togglePw() {
    const input = document.getElementById('drawerPw');
    const eyeOn = document.getElementById('eyeOpen');
    const eyeOff = document.getElementById('eyeClosed');
    if (input.type === 'password') {
        input.type = 'text'; eyeOn.style.display = 'none'; eyeOff.style.display = 'block';
    } else {
        input.type = 'password'; eyeOn.style.display = 'block'; eyeOff.style.display = 'none';
    }
}

let currentStep = 1;
const totalSteps = 4;
const stepColors = ['#2563eb','#1d4ed8','#1e40af','#1e3a8a'];
let formData = { name:'', email:'', password:'', confirm:'', telefono:'', role:'' };

function saveCurrentStep() {
    switch(currentStep) {
        case 1: formData.name     = document.getElementById('f_name')?.value.trim()     || ''; break;
        case 2:
            formData.email    = document.getElementById('f_email')?.value.trim()    || '';
            formData.password = document.getElementById('f_pw')?.value              || '';
            formData.confirm  = document.getElementById('f_confirm')?.value         || '';
            break;
        case 3: formData.telefono = document.getElementById('f_telefono')?.value.trim()  || ''; break;
        case 4: formData.role     = document.getElementById('f_type')?.value             || ''; break;
    }
}

const steps = [
    {
        title: 'Tu perfil', subtitle: '¿Cómo te llamas?',
        render: () => `
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Nombre completo</label>
                <input id="f_name" type="text" placeholder="Juan García" value="${formData.name}"
                       class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200"
                       oninput="renderNav()">
            </div>`,
        valid: () => (document.getElementById('f_name')?.value.trim().length > 1)
    },
    {
        title: 'Acceso', subtitle: 'Crea tus credenciales',
        render: () => `
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Correo electrónico</label>
                <input id="f_email" type="email" placeholder="tu@correo.com" value="${formData.email}"
                       class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200"
                       oninput="renderNav()">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Contraseña</label>
                <div class="relative">
                    <input id="f_pw" type="password" placeholder="Mínimo 6 caracteres" value="${formData.password}"
                           class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 pr-11 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200"
                           oninput="checkPwMatch()">
                    <button type="button" onclick="toggleRegPw('f_pw','eye_pw1','eye_pw1c')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg id="eye_pw1" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="eye_pw1c" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>
            <div class="mb-2">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Confirmar contraseña</label>
                <div class="relative">
                    <input id="f_confirm" type="password" placeholder="Repite tu contraseña" value="${formData.confirm}"
                           class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 pr-11 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200"
                           oninput="checkPwMatch()">
                    <button type="button" onclick="toggleRegPw('f_confirm','eye_pw2','eye_pw2c')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg id="eye_pw2" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="eye_pw2c" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                <p id="pwMatchMsg" class="text-xs mt-1.5 hidden"></p>
            </div>`,
        valid: () => {
            const pw = document.getElementById('f_pw')?.value;
            const cf = document.getElementById('f_confirm')?.value;
            return document.getElementById('f_email')?.value.includes('@') && pw?.length >= 6 && pw === cf;
        }
    },
    {
        title: 'Contacto', subtitle: 'Tu número de teléfono',
        render: () => `
            <div class="mb-2">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-1.5">Número de teléfono</label>
                <input id="f_telefono" type="tel" placeholder="+57 300 000 0000" value="${formData.telefono}"
                       class="kliksy-input w-full bg-slate-100 border-2 border-transparent rounded-xl px-4 py-3 text-slate-900 text-sm placeholder-slate-400 transition-all duration-200"
                       oninput="renderNav()">
                <p class="text-xs text-slate-400 mt-2">Solo lo usaremos para confirmar tu cuenta.</p>
            </div>`,
        valid: () => (document.getElementById('f_telefono')?.value.replace(/\D/g,'').length >= 7)
    },
    {
        title: 'Tipo de cuenta', subtitle: '¿Cómo usarás Kliksy?',
        render: () => `
            <div class="flex flex-col gap-3">
                <button type="button" onclick="selectType('cliente')"
                        class="w-full text-left px-5 py-4 rounded-2xl border-2 ${formData.role==='cliente' ? 'border-blue-600 bg-blue-50 shadow-md shadow-blue-100' : 'border-slate-200 bg-white'} flex items-center gap-4 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl ${formData.role==='cliente' ? 'bg-blue-600' : 'bg-slate-100'} flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${formData.role==='cliente' ? 'white' : '#64748b'}" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-sm">Soy cliente</div>
                        <div class="text-xs text-slate-500 mt-0.5">Busco profesionales para mis necesidades</div>
                    </div>
                    ${formData.role==='cliente' ? '<div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0"><svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-6" stroke="white" stroke-width="2" stroke-linecap="round"/></svg></div>' : ''}
                </button>
                <button type="button" onclick="selectType('profesional')"
                        class="w-full text-left px-5 py-4 rounded-2xl border-2 ${formData.role==='profesional' ? 'border-blue-600 bg-blue-50 shadow-md shadow-blue-100' : 'border-slate-200 bg-white'} flex items-center gap-4 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl ${formData.role==='profesional' ? 'bg-blue-600' : 'bg-slate-100'} flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${formData.role==='profesional' ? 'white' : '#64748b'}" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v4M10 14h4"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-sm">Soy profesional</div>
                        <div class="text-xs text-slate-500 mt-0.5">Ofrezco mis servicios en la plataforma</div>
                    </div>
                    ${formData.role==='profesional' ? '<div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0"><svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-6" stroke="white" stroke-width="2" stroke-linecap="round"/></svg></div>' : ''}
                </button>
                <input type="hidden" id="f_type" value="${formData.role}">
            </div>`,
        valid: () => (formData.role !== '')
    }
];

function selectType(type) {
    formData.role = type;
    document.getElementById('stepContent').innerHTML = `<div class="step-enter">${steps[3].render()}</div>`;
    renderNav();
}

function checkPwMatch() {
    const pw  = document.getElementById('f_pw')?.value;
    const cf  = document.getElementById('f_confirm')?.value;
    const msg = document.getElementById('pwMatchMsg');
    if (!msg || !cf) return;
    if (pw === cf) {
        msg.textContent = '✓ Las contraseñas coinciden';
        msg.className = 'text-xs mt-1.5 text-green-600';
        msg.classList.remove('hidden');
    } else {
        msg.textContent = 'Las contraseñas no coinciden';
        msg.className = 'text-xs mt-1.5 text-red-500';
        msg.classList.remove('hidden');
    }
    renderNav();
}

function toggleRegPw(inputId, openId, closedId) {
    const input  = document.getElementById(inputId);
    const eyeOn  = document.getElementById(openId);
    const eyeOff = document.getElementById(closedId);
    if (input.type === 'password') {
        input.type = 'text'; eyeOn.style.display = 'none'; eyeOff.style.display = 'block';
    } else {
        input.type = 'password'; eyeOn.style.display = 'block'; eyeOff.style.display = 'none';
    }
}

function renderStep() {
    const s = steps[currentStep - 1];
    document.getElementById('regTop').style.backgroundColor = stepColors[currentStep - 1];
    document.getElementById('regTitle').textContent    = s.title;
    document.getElementById('regSubtitle').textContent = s.subtitle;

    const dots = document.getElementById('stepDots');
    dots.innerHTML = '';
    for (let i = 1; i <= totalSteps; i++) {
        const done = i < currentStep, active = i === currentStep;
        const el = document.createElement('div');
        el.style.transition = 'all 0.3s';
        if (done) {
            el.className = 'w-6 h-6 rounded-full bg-white flex items-center justify-center text-blue-600 text-xs font-black';
            el.innerHTML = `<svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
        } else if (active) {
            el.className = 'w-7 h-6 rounded-full bg-white flex items-center justify-center text-blue-600 text-xs font-black';
            el.textContent = i;
        } else {
            el.className = 'w-6 h-6 rounded-full bg-white/25 flex items-center justify-center text-white text-xs font-bold';
            el.textContent = i;
        }
        dots.appendChild(el);
        if (i < totalSteps) {
            const line = document.createElement('div');
            line.className = `h-0.5 w-5 rounded-full ${i < currentStep ? 'bg-white/80' : 'bg-white/25'}`;
            line.style.transition = 'background 0.3s';
            dots.appendChild(line);
        }
    }

    document.getElementById('stepContent').innerHTML = `<div class="step-enter">${s.render()}</div>`;
    renderNav();
}

function renderNav() {
    const valid  = steps[currentStep - 1].valid();
    const isLast = currentStep === totalSteps;
    const nav    = document.getElementById('stepNav');
    const footer = document.getElementById('stepFooter');
    nav.innerHTML = '';

    if (currentStep > 1) {
        const back = document.createElement('button');
        back.type = 'button';
        back.className = 'flex items-center justify-center w-12 h-12 rounded-xl border-2 border-slate-200 text-slate-500 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 flex-shrink-0';
        back.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>`;
        back.onclick = () => { saveCurrentStep(); currentStep--; renderStep(); };
        nav.appendChild(back);
    }

    const next = document.createElement('button');
    next.type = 'button';
    next.disabled = !valid;
    next.className = `flex-1 flex items-center justify-center gap-2 py-3.5 rounded-xl text-white font-bold text-sm transition-all duration-200 ${
        valid ? 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 hover:-translate-y-0.5' : 'bg-blue-300 cursor-not-allowed'
    }`;

    if (isLast) {
        next.innerHTML = `Crear mi cuenta`;
        next.onclick = () => { if (valid) { saveCurrentStep(); submitRegister(); } };
    } else {
        next.innerHTML = `Continuar <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>`;
        next.onclick = () => { if (valid) { saveCurrentStep(); currentStep++; renderStep(); } };
    }
    nav.appendChild(next);

    footer.innerHTML = `Paso ${currentStep} de ${totalSteps} &nbsp;·&nbsp;
        <button type="button" onclick="closeAll(); openLogin()"
                class="text-blue-600 font-semibold hover:underline bg-transparent border-none cursor-pointer text-xs">
            Ya tengo cuenta
        </button>`;
}

function submitRegister() {
    document.getElementById('hf_name').value     = formData.name;
    document.getElementById('hf_email').value    = formData.email;
    document.getElementById('hf_password').value = formData.password;
    document.getElementById('hf_confirm').value  = formData.confirm;
    document.getElementById('hf_telefono').value = formData.telefono;
    document.getElementById('hf_role').value     = formData.role;
    document.getElementById('regForm').submit();
}
// Limpiar estado del drawer al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('pageWrap').classList.remove('drawer-open');
    document.getElementById('drawerOverlay').classList.add('hidden');
});
</script>

{{-- DRAWERS CLIENTE --}}
@auth
@if(auth()->user()->role_id === 1)
<div id="historialOverlay" onclick="closeHistorial()"
    class="fixed inset-0 bg-gray-900/45 z-[100] hidden cursor-pointer"></div>

<aside id="historialDrawer"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-[420px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl"
    style="transform:translateX(100%);transition:transform 0.55s cubic-bezier(.77,0,.18,1);">

    <div class="relative bg-blue-600 px-8 pt-8 pb-7 flex-shrink-0 overflow-hidden">
        <div style="position:absolute;width:280px;height:280px;border-radius:50%;border:40px solid rgba(255,255,255,0.08);top:-80px;right:-80px;pointer-events:none;"></div>
        <button onclick="closeHistorial()"
            class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">✕</button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy</span>
        </div>
        <div class="relative z-10">
            <h2 class="text-3xl text-white font-bold leading-tight mb-1.5">Mi<br>historial</h2>
            <p class="text-sm text-white/70">
                {{ isset($historialCliente) && $historialCliente->count() > 0 ? $historialCliente->count() . ' servicios completados' : 'Sin servicios completados aún' }}
            </p>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-5 pt-5 pb-8 flex flex-col gap-3">
        @if(isset($historialCliente) && $historialCliente->count())
        @foreach($historialCliente as $sol)
        <div class="border border-gray-200 rounded-2xl p-4 hover:border-blue-200 transition">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($sol->profesional->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $sol->profesional->name }}</p>
                    <p class="text-xs text-gray-400">{{ $sol->updated_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-medium flex-shrink-0">Completado</span>
            </div>
            <p class="text-sm text-gray-500 mb-3 flex items-center gap-1.5">
                <i class="bi bi-scissors text-blue-400 flex-shrink-0"></i>
                {{ $sol->servicio->titulo }}
            </p>
            @if(!in_array($sol->servicio_id, $resenasHechas ?? []))
            <a href="{{ route('profesional.publico', $sol->profesional->id) }}?tab=resenas"
                class="w-full flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm py-2 rounded-xl transition">
                <i class="bi bi-star"></i> Dejar reseña
            </a>
            @else
            <p class="text-xs text-center text-gray-400 flex items-center justify-center gap-1">
                <i class="bi bi-check-circle text-green-500"></i> Reseña enviada
            </p>
            @endif
        </div>
        @endforeach
        @else
        <div class="flex-1 flex flex-col items-center justify-center text-center py-16 text-gray-400">
            <i class="bi bi-clock-history text-5xl mb-4 block"></i>
            <p class="font-medium">Sin historial aún</p>
            <p class="text-sm mt-1">Aquí aparecerán los servicios que hayas completado</p>
        </div>
        @endif
    </div>
</aside>

<script>
function openHistorial() {
    document.getElementById('historialDrawer').style.transform = 'translateX(0)';
    document.getElementById('historialOverlay').classList.remove('hidden');
}
function closeHistorial() {
    document.getElementById('historialDrawer').style.transform = 'translateX(100%)';
    document.getElementById('historialOverlay').classList.add('hidden');
}
</script>
@endif
@endauth

</body>
</html>