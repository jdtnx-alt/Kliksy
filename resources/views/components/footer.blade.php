<footer class="bg-white w-full mt-12 sm:mt-20">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-16 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-8 sm:gap-10">

        {{-- Logo y descripción --}}
        <div class="col-span-2 sm:col-span-2 md:col-span-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-500 text-white w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg font-bold text-sm sm:text-base flex-shrink-0">
                    K
                </div>
                <span class="text-lg sm:text-xl font-semibold text-gray-800">Kliksy</span>
            </div>
            <p class="text-gray-500 text-sm leading-relaxed">
                Tu marketplace de confianza para servicios a domicilio.
                Conectamos profesionales calificados con quienes los necesitan.
            </p>
        </div>

        {{-- Navegación --}}
        <div>
            <h3 class="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Navegación</h3>
            <ul class="space-y-2 text-gray-500 text-sm">
                <li><a href="/" class="hover:text-blue-500 transition">Inicio</a></li>
                <li><a href="/servicios" class="hover:text-blue-500 transition">Servicios</a></li>
                <li><a href="/register" class="hover:text-blue-500 transition">Registrarse</a></li>
            </ul>
        </div>

        {{-- Categorías --}}
        <div>
            <h3 class="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Categorías</h3>
<ul class="space-y-2 text-gray-500 text-sm">
    @foreach(\App\Helpers\CategoriaHelper::padres() as $slug => $nombre)
    <li>
        <a href="{{ route('servicios.index', ['categoria' => $slug]) }}"
            class="hover:text-blue-500 transition">
            {{ $nombre }}
        </a>
    </li>
    @endforeach
</ul>
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Contacto</h3>
            <ul class="space-y-2 text-gray-500 text-sm">
                <li class="flex items-center gap-2">
                    <i class="bi bi-envelope text-blue-400 flex-shrink-0"></i>
                    <span>info@kliksy.com</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="bi bi-telephone text-blue-400 flex-shrink-0"></i>
                    <span>+57 311 522 9975</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="bi bi-geo-alt text-blue-400 flex-shrink-0"></i>
                    <span>Florencia, Caquetá, Colombia</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="border-t border-gray-200"></div>

    <div class="text-center text-gray-400 text-xs sm:text-sm py-5 px-4">
        © 2026 Kliksy. Todos los derechos reservados.
    </div>

</footer>