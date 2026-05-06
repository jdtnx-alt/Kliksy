<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\PerfilProfesional;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfesionalSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------
        // DATOS: cada entrada = un profesional con sus servicios
        // ---------------------------------------------------------------
        $profesionales = [

            // ===== BELLEZA PERSONAL =====
            [
                'name' => 'Ingri Julieth Gascate Norio',
                'email' => 'ingrijuliethgascatenorio@gmail.com',
                'telefono' => '3101234567',
                'perfil' => [
                    'descripcion' => 'Estilista profesional con 8 años de experiencia en peluquería femenina y maquillaje para eventos. Trabajo con productos de alta calidad y garantizo resultados impecables.',
                    'experiencia' => '8 años',
                    'ubicacion' => 'Bogotá, Chapinero',
                    'whatsapp' => '3101234567',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Maquillaje profesional para novias', 'descripcion' => 'Maquillaje de larga duración para tu día especial. Incluye prueba previa y maquillaje el día del evento con productos MAC y Fenty Beauty.', 'precio' => 180000, 'categoria' => 'belleza', 'subcategoria' => 'maquillaje'],
                    ['titulo' => 'Corte y tinte femenino a domicilio', 'descripcion' => 'Corte personalizado según tu tipo de rostro, con aplicación de tinte o mechas balayage. Visita tu hogar en Bogotá.', 'precio' => 120000, 'categoria' => 'belleza', 'subcategoria' => 'peluqueria'],
                    ['titulo' => 'Extensión de pestañas pelo a pelo', 'descripcion' => 'Extensiones de pestañas naturales pelo a pelo, duración de 3 a 4 semanas. Incluye mantenimiento gratuito a los 15 días.', 'precio' => 95000, 'categoria' => 'belleza', 'subcategoria' => 'pestanas'],
                ],
                'negocio' => [
                    'nombre' => 'Ingri Beauty Studio',
                    'descripcion' => 'Estudio de belleza a domicilio especializado en maquillaje artístico, extensión de pestañas y colorimetría. Atención personalizada en tu hogar o en nuestro studio en Chapinero.',
                    'direccion' => 'Cra 13 #63-40, Chapinero, Bogotá',
                    'telefono' => '3101234567',
                    'categoria' => 'belleza',
                ],
            ],
            [
                'name' => 'Camilo Estrada Parra',
                'email' => 'camilo.estrada.kliksy@example.com',
                'telefono' => '3178901234',
                'perfil' => [
                    'descripcion' => 'Barbero certificado con experiencia en cortes clásicos, degradados y diseño de barba. Atención a domicilio en toda Bogotá.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Kennedy',
                    'whatsapp' => '3178901234',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Corte y arreglo de barba a domicilio', 'descripcion' => 'Corte moderno con máquina y tijera, más diseño y perfilado de barba con navaja. Llevo todos mis implementos esterilizados.', 'precio' => 40000, 'categoria' => 'belleza', 'subcategoria' => 'barberia'],
                    ['titulo' => 'Degradado y fade americano', 'descripcion' => 'Especialista en degradados bajos, medios y altos. Servicio premium a domicilio con productos Wahl y Andis.', 'precio' => 50000, 'categoria' => 'belleza', 'subcategoria' => 'barberia'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Mariana Ospina Cárdenas',
                'email' => 'mariana.ospina.kliksy@example.com',
                'telefono' => '3189012345',
                'perfil' => [
                    'descripcion' => 'Manicurista y pedicurista certificada. Especialista en uñas acrílicas, gelish y nail art. Llevo todos los implementos esterilizados a tu hogar.',
                    'experiencia' => '4 años',
                    'ubicacion' => 'Bogotá, Suba',
                    'whatsapp' => '3189012345',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Uñas acrílicas con nail art', 'descripcion' => 'Esculpido de uñas acrílicas con diseño personalizado. Incluye base, gel color y top coat. Durabilidad garantizada de 3 semanas.', 'precio' => 85000, 'categoria' => 'belleza', 'subcategoria' => 'manicura'],
                    ['titulo' => 'Manicura y pedicura spa', 'descripcion' => 'Servicio completo de manicura y pedicura con exfoliación, hidratación y esmaltado semipermanente. Pura relajación en casa.', 'precio' => 65000, 'categoria' => 'belleza', 'subcategoria' => 'manicura'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Tatiana Moreno Salcedo',
                'email' => 'tatiana.moreno.kliksy@example.com',
                'telefono' => '3190123456',
                'perfil' => [
                    'descripcion' => 'Especialista en depilación con cera caliente, fría y técnica de hilo. Atención discreta y profesional en la comodidad de tu hogar.',
                    'experiencia' => '6 años',
                    'ubicacion' => 'Bogotá, Usaquén',
                    'whatsapp' => '3190123456',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Depilación corporal completa a domicilio', 'descripcion' => 'Depilación con cera caliente de piernas, axilas, bikini y más zonas. Incluye aceite calmante post-depilación. Zona Bogotá norte y centro.', 'precio' => 70000, 'categoria' => 'belleza', 'subcategoria' => 'depilacion'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Rodrigo Salas Pinzón',
                'email' => 'rodrigo.salas.kliksy@example.com',
                'telefono' => '3201234560',
                'perfil' => [
                    'descripcion' => 'Tatuador profesional con más de 200 diseños realizados. Especialista en realismo, blackwork y diseños minimalistas. Llevo equipo esterilizado a domicilio.',
                    'experiencia' => '7 años',
                    'ubicacion' => 'Bogotá, Teusaquillo',
                    'whatsapp' => '3201234560',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Tatuaje a domicilio - diseño personalizado', 'descripcion' => 'Sesión de tatuaje en tu hogar. Equipo completamente esterilizado y descartable. El precio varía según el tamaño; este es el valor base por hora de trabajo.', 'precio' => 120000, 'categoria' => 'belleza', 'subcategoria' => 'tatuajes'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Lorena Quintero Ávila',
                'email' => 'lorena.quintero.kliksy@example.com',
                'telefono' => '3212345601',
                'perfil' => [
                    'descripcion' => 'Terapeuta certificada en masajes relajantes, descontracturantes y faciales. Servicio de spa completo a domicilio con camilla, música y aromaterapia.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Fontibón',
                    'whatsapp' => '3212345601',
                    'categorias' => ['belleza'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Spa relajante a domicilio', 'descripcion' => 'Sesión de 90 minutos que incluye masaje relajante con aceites esenciales, exfoliación corporal y mascarilla facial. Llevo camilla y ambiente spa a tu hogar.', 'precio' => 130000, 'categoria' => 'belleza', 'subcategoria' => 'spa_domicilio'],
                ],
                'negocio' => null,
            ],

            // ===== HOGAR =====
            [
                'name' => 'Juan David Torres Nieto',
                'email' => 'juandavidtn4@gmail.com',
                'telefono' => '3223456012',
                'perfil' => [
                    'descripcion' => 'Técnico en mantenimiento del hogar con experiencia en pintura, montaje de muebles y reparaciones generales. Trabajo limpio, puntual y con garantía.',
                    'experiencia' => '6 años',
                    'ubicacion' => 'Bogotá, Bosa',
                    'whatsapp' => '3223456012',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Pintura de interiores y exteriores', 'descripcion' => 'Pintura de habitaciones, salas, cocinas y fachadas. Incluye preparación de superficies, estuco si se requiere y pintura de alta calidad. Trabajo limpio con protección de pisos y muebles.', 'precio' => 80000, 'categoria' => 'hogar', 'subcategoria' => 'pintura'],
                    ['titulo' => 'Montaje y armado de muebles', 'descripcion' => 'Armado de muebles de cocina, closets, camas, escritorios y estantes. Trabajo con herramientas profesionales. Experiencia con muebles RTA y de cualquier marca.', 'precio' => 50000, 'categoria' => 'hogar', 'subcategoria' => 'montaje'],
                    ['titulo' => 'Aseo profundo del hogar', 'descripcion' => 'Limpieza profunda de apartamentos y casas. Incluye baños, cocina, ventanas y pisos. Llevo implementos y productos de limpieza de alta eficacia.', 'precio' => 90000, 'categoria' => 'hogar', 'subcategoria' => 'aseo'],
                ],
                'negocio' => [
                    'nombre' => 'JD Mantenimientos Hogar',
                    'descripcion' => 'Servicio integral de mantenimiento del hogar: pintura, aseo profundo, montaje de muebles y reparaciones menores. Atención rápida en toda Bogotá sur y occidente.',
                    'direccion' => 'Cl 38 Sur #72-15, Bosa, Bogotá',
                    'telefono' => '3223456012',
                    'categoria' => 'hogar',
                ],
            ],
            [
                'name' => 'Patricia Lozano Bejarano',
                'email' => 'patricia.lozano.kliksy@example.com',
                'telefono' => '3234560123',
                'perfil' => [
                    'descripcion' => 'Especialista en fumigación y control de plagas. Productos certificados y seguros para mascotas y niños. Garantía de 3 meses.',
                    'experiencia' => '9 años',
                    'ubicacion' => 'Bogotá, Engativá',
                    'whatsapp' => '3234560123',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Fumigación de apartamento o casa', 'descripcion' => 'Control de cucarachas, hormigas, pulgas, mosquitos y más plagas. Productos biodegradables y seguros. Garantía de efectividad de 3 meses. Bogotá y alrededores.', 'precio' => 85000, 'categoria' => 'hogar', 'subcategoria' => 'fumigacion'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Hernán Castaño Melo',
                'email' => 'hernan.castano.kliksy@example.com',
                'telefono' => '3245601234',
                'perfil' => [
                    'descripcion' => 'Jardinero con formación técnica en paisajismo. Diseño, mantenimiento y adecuación de jardines residenciales y comerciales.',
                    'experiencia' => '10 años',
                    'ubicacion' => 'Bogotá, Usaquén',
                    'whatsapp' => '3245601234',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Mantenimiento mensual de jardín', 'descripcion' => 'Servicio mensual de jardinería: poda, abono, control de maleza, riego y revisión fitosanitaria de plantas. Jardines residenciales y conjuntos.', 'precio' => 70000, 'categoria' => 'hogar', 'subcategoria' => 'jardineria'],
                    ['titulo' => 'Diseño y adecuación de jardín', 'descripcion' => 'Diseño personalizado de tu espacio verde: selección de plantas, instalación de sustrato, riego y decoración paisajística. Presupuesto sin costo.', 'precio' => 250000, 'categoria' => 'hogar', 'subcategoria' => 'jardineria'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Óscar Bermúdez Fuentes',
                'email' => 'oscar.bermudez.kliksy@example.com',
                'telefono' => '3256012345',
                'perfil' => [
                    'descripcion' => 'Cerrajero profesional con atención 24/7. Apertura de puertas, cambio de chapas y duplicación de llaves. Respuesta rápida en toda Bogotá.',
                    'experiencia' => '12 años',
                    'ubicacion' => 'Bogotá, Centro',
                    'whatsapp' => '3256012345',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Apertura de puertas 24/7', 'descripcion' => 'Apertura de puertas sin daño, rápida y segura. Atención de emergencias los 7 días de la semana. Llegada en menos de 40 minutos en Bogotá.', 'precio' => 80000, 'categoria' => 'hogar', 'subcategoria' => 'cerrajeria'],
                    ['titulo' => 'Cambio e instalación de chapas', 'descripcion' => 'Instalación de chapas de seguridad, cerraduras de alta seguridad y puertas blindadas. Asesoría gratuita sobre el mejor sistema para tu hogar.', 'precio' => 60000, 'categoria' => 'hogar', 'subcategoria' => 'cerrajeria'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Claudia Nieto Serrano',
                'email' => 'claudia.nieto.kliksy@example.com',
                'telefono' => '3267890123',
                'perfil' => [
                    'descripcion' => 'Técnica en impermeabilización de terrazas, cubiertas y baños. Soluciones definitivas contra filtraciones y goteras. Materiales de primera calidad.',
                    'experiencia' => '7 años',
                    'ubicacion' => 'Bogotá, Barrios Unidos',
                    'whatsapp' => '3267890123',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Impermeabilización de terraza y cubierta', 'descripcion' => 'Aplicación de membrana asfáltica y manto impermeabilizante en terrazas y cubiertas. Garantía de 2 años. Diagnóstico de filtraciones sin costo.', 'precio' => 200000, 'categoria' => 'hogar', 'subcategoria' => 'impermeabil'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Fabio Acosta Villalba',
                'email' => 'fabio.acosta.kliksy@example.com',
                'telefono' => '3278901234',
                'perfil' => [
                    'descripcion' => 'Lavado de alfombras, tapetes y tapizados a domicilio con máquina extractora. Secado rápido en 2-3 horas. Productos antiácaros e hipoalergénicos.',
                    'experiencia' => '4 años',
                    'ubicacion' => 'Bogotá, Puente Aranda',
                    'whatsapp' => '3278901234',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Lavado de alfombras y tapetes a domicilio', 'descripcion' => 'Lavado profundo con máquina extractora de agua. Elimina ácaros, bacterias y manchas difíciles. Secado en 2-3 horas. Precio por m².', 'precio' => 15000, 'categoria' => 'hogar', 'subcategoria' => 'lavado_alfombra'],
                    ['titulo' => 'Lavado de sofás y tapizados', 'descripcion' => 'Limpieza profunda de sofás, sillas y muebles tapizados. Eliminación de manchas, olores y alérgenos. Precio por puesto.', 'precio' => 35000, 'categoria' => 'hogar', 'subcategoria' => 'lavado_alfombra'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Beatriz Aguilar Peña',
                'email' => 'beatriz.aguilar.kliksy@example.com',
                'telefono' => '3289012345',
                'perfil' => [
                    'descripcion' => 'Auxiliar de mudanzas con flota propia. Empaque, transporte y desempaque de trasteos en Bogotá y municipios. Servicio rápido y con cuidado de tus objetos.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Ciudad Bolívar',
                    'whatsapp' => '3289012345',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Trasteo completo en Bogotá', 'descripcion' => 'Servicio de mudanza con vehículo de carga, empaque con plástico burbuja, transporte y acomodación en destino. Cuidamos tus muebles y electrodomésticos.', 'precio' => 200000, 'categoria' => 'hogar', 'subcategoria' => 'mudanzas'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Nelson Rincón Tovar',
                'email' => 'nelson.rincon.kliksy@example.com',
                'telefono' => '3290123456',
                'perfil' => [
                    'descripcion' => 'Técnico certificado en reparación de electrodomésticos: neveras, lavadoras, estufas y microondas. Repuestos originales y garantía de mano de obra.',
                    'experiencia' => '11 años',
                    'ubicacion' => 'Bogotá, Rafael Uribe',
                    'whatsapp' => '3290123456',
                    'categorias' => ['hogar'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Reparación de lavadoras a domicilio', 'descripcion' => 'Diagnóstico y reparación de lavadoras de todas las marcas: Samsung, LG, Haceb, Whirlpool. Repuestos originales. Garantía de 3 meses en mano de obra.', 'precio' => 60000, 'categoria' => 'hogar', 'subcategoria' => 'electrodomesticos'],
                    ['titulo' => 'Reparación de neveras y refrigeradores', 'descripcion' => 'Recarga de gas, reparación de compresor, termostato y más. Todas las marcas. Visita de diagnóstico sin costo adicional si se realiza la reparación.', 'precio' => 80000, 'categoria' => 'hogar', 'subcategoria' => 'electrodomesticos'],
                ],
                'negocio' => null,
            ],

            // ===== INSTALACIONES =====
            [
                'name' => 'Juan David Nieto Torres',
                'email' => 'juandavidnt4@gmail.com',
                'telefono' => '3301234567',
                'perfil' => [
                    'descripcion' => 'Electricista certificado RETIE con 10 años de experiencia en instalaciones residenciales y comerciales. Trabajo limpio, seguro y con garantía.',
                    'experiencia' => '10 años',
                    'ubicacion' => 'Bogotá, Soacha',
                    'whatsapp' => '3301234567',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Instalaciones eléctricas residenciales', 'descripcion' => 'Instalación de tomacorrientes, interruptores, puntos de luz, tableros y circuitos. Certificación RETIE. Trabajo con materiales de primera y garantía de 1 año.', 'precio' => 70000, 'categoria' => 'instalaciones', 'subcategoria' => 'electricidad'],
                    ['titulo' => 'Revisión y mantenimiento eléctrico', 'descripcion' => 'Diagnóstico completo de la red eléctrica de tu hogar. Detección de cortocircuitos, sobrecargas y puntos peligrosos. Informe técnico incluido.', 'precio' => 50000, 'categoria' => 'instalaciones', 'subcategoria' => 'electricidad'],
                    ['titulo' => 'Instalación de cámaras y alarmas', 'descripcion' => 'Instalación de sistemas CCTV, cámaras IP, alarmas perimetrales e intercomunicadores. Configuración remota desde tu celular. Asesoría de seguridad gratuita.', 'precio' => 150000, 'categoria' => 'instalaciones', 'subcategoria' => 'alarmas'],
                ],
                'negocio' => [
                    'nombre' => 'JD Soluciones Eléctricas',
                    'descripcion' => 'Empresa de instalaciones eléctricas y seguridad electrónica. Especializados en redes eléctricas, cámaras de seguridad y sistemas de alarma para hogares y empresas en Bogotá y Soacha.',
                    'direccion' => 'Cra 7 #15-20, Soacha, Cundinamarca',
                    'telefono' => '3301234567',
                    'categoria' => 'instalaciones',
                ],
            ],
            [
                'name' => 'Gustavo Pedraza Ríos',
                'email' => 'gustavo.pedraza.kliksy@example.com',
                'telefono' => '3312345678',
                'perfil' => [
                    'descripcion' => 'Plomero con más de 15 años de experiencia. Destape de cañerías, instalación de sanitarios, grifería y tuberías. Atención de urgencias 24/7.',
                    'experiencia' => '15 años',
                    'ubicacion' => 'Bogotá, Tunjuelito',
                    'whatsapp' => '3312345678',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Destape de cañerías y alcantarillado', 'descripcion' => 'Destape de tuberías, lavamanos, sanitarios y cañerías con equipo especializado. Diagnóstico con cámara. Solución garantizada o no cobro.', 'precio' => 60000, 'categoria' => 'instalaciones', 'subcategoria' => 'plomeria'],
                    ['titulo' => 'Instalación de sanitarios y grifería', 'descripcion' => 'Instalación y cambio de sanitarios, lavamanos, duchas, llaves y grifería en general. Revisión de tuberías incluida. Garantía de 6 meses.', 'precio' => 80000, 'categoria' => 'instalaciones', 'subcategoria' => 'plomeria'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Ernesto Varela Cubillos',
                'email' => 'ernesto.varela.kliksy@example.com',
                'telefono' => '3323456789',
                'perfil' => [
                    'descripcion' => 'Técnico certificado en instalación y mantenimiento de aires acondicionados. Todas las marcas: Carrier, LG, Samsung, Mitsubishi. Servicio en Bogotá.',
                    'experiencia' => '8 años',
                    'ubicacion' => 'Bogotá, Teusaquillo',
                    'whatsapp' => '3323456789',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Instalación de aire acondicionado split', 'descripcion' => 'Instalación profesional de unidades split de 9.000 a 36.000 BTU. Incluye cargue de refrigerante, pruebas de funcionamiento y asesoría de mantenimiento.', 'precio' => 180000, 'categoria' => 'instalaciones', 'subcategoria' => 'aires'],
                    ['titulo' => 'Mantenimiento y limpieza de aires acondicionados', 'descripcion' => 'Limpieza profunda de filtros, evaporador y condensador. Revisión de niveles de refrigerante. Extiende la vida útil de tu equipo y reduce el consumo eléctrico.', 'precio' => 80000, 'categoria' => 'instalaciones', 'subcategoria' => 'aires'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Jhon Arbeláez Montoya',
                'email' => 'jhon.arbelaez.kliksy@example.com',
                'telefono' => '3334567890',
                'perfil' => [
                    'descripcion' => 'Técnico en redes e internet. Instalación de routers, puntos de acceso WiFi, cableado estructurado y fibra óptica para hogares y empresas.',
                    'experiencia' => '6 años',
                    'ubicacion' => 'Bogotá, Chapinero',
                    'whatsapp' => '3334567890',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Instalación y configuración de red WiFi', 'descripcion' => 'Configuración de routers, repetidores y sistemas mesh para cobertura total en tu hogar u oficina. Optimización de canales y seguridad de red. Bogotá.', 'precio' => 60000, 'categoria' => 'instalaciones', 'subcategoria' => 'redes'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Mario Espitia Contreras',
                'email' => 'mario.espitia.kliksy@example.com',
                'telefono' => '3345678901',
                'perfil' => [
                    'descripcion' => 'Soldador certificado con experiencia en rejas, portones, escaleras y estructuras metálicas. Trabajo a domicilio con equipo propio de soldadura MIG y TIG.',
                    'experiencia' => '9 años',
                    'ubicacion' => 'Bogotá, Bosa',
                    'whatsapp' => '3345678901',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Fabricación e instalación de rejas', 'descripcion' => 'Fabricación de rejas para ventanas, puertas y garajes en hierro o aluminio. Diseños modernos y clásicos. Instalación a domicilio en toda Bogotá.', 'precio' => 120000, 'categoria' => 'instalaciones', 'subcategoria' => 'soldadura'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Álvaro Caicedo Salazar',
                'email' => 'alvaro.caicedo.kliksy@example.com',
                'telefono' => '3356789012',
                'perfil' => [
                    'descripcion' => 'Técnico en gas natural certificado. Instalación, revisión y mantenimiento de redes de gas para hogares. Seguridad garantizada.',
                    'experiencia' => '7 años',
                    'ubicacion' => 'Bogotá, Fontibón',
                    'whatsapp' => '3356789012',
                    'categorias' => ['instalaciones'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Revisión e instalación de red de gas', 'descripcion' => 'Revisión de tuberías de gas, instalación de estufas y calentadores. Detección de fugas con equipo certificado. Trabajo avalado por Gas Natural. Bogotá.', 'precio' => 70000, 'categoria' => 'instalaciones', 'subcategoria' => 'gas'],
                ],
                'negocio' => null,
            ],

            // ===== VEHÍCULOS =====
            [
                'name' => 'Juan David Nieto X',
                'email' => 'jdtnxxx@gmail.com',
                'telefono' => '3367890123',
                'perfil' => [
                    'descripcion' => 'Mecánico automotriz con 8 años de experiencia. Diagnóstico y reparación a domicilio de carros y motos. Revisión de frenos, suspensión, motor y más.',
                    'experiencia' => '8 años',
                    'ubicacion' => 'Bogotá, Kennedy',
                    'whatsapp' => '3367890123',
                    'categorias' => ['vehiculos'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Mecánica general a domicilio', 'descripcion' => 'Revisión y reparación de motor, frenos, suspensión, cambio de aceite y más. Llevo herramienta y escáner diagnóstico. Servicio en Bogotá occidental y sur.', 'precio' => 60000, 'categoria' => 'vehiculos', 'subcategoria' => 'mecanica'],
                    ['titulo' => 'Lavado de carro a domicilio', 'descripcion' => 'Lavado exterior e interior de tu vehículo en tu casa o trabajo. Incluye aspirado, limpieza de tapicería y brillado de llantas. Sin consumo de agua de tu hogar.', 'precio' => 35000, 'categoria' => 'vehiculos', 'subcategoria' => 'lavado_auto'],
                    ['titulo' => 'Cambio de llantas y balanceo', 'descripcion' => 'Cambio de llantas a domicilio con equipo portátil de balanceo y alineación. Todas las marcas y referencias. Precios competitivos con garantía de trabajo.', 'precio' => 20000, 'categoria' => 'vehiculos', 'subcategoria' => 'llantas'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Jhonatan Giraldo Mesa',
                'email' => 'jhonatan.giraldo.kliksy@example.com',
                'telefono' => '3378901234',
                'perfil' => [
                    'descripcion' => 'Servicio de domicilios y mensajería en moto por Bogotá. Pago contra entrega, encomiendas, documentos y paquetes. Rápido y confiable.',
                    'experiencia' => '3 años',
                    'ubicacion' => 'Bogotá, Usme',
                    'whatsapp' => '3378901234',
                    'categorias' => ['vehiculos'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Domicilios y mensajería en moto - Bogotá', 'descripcion' => 'Recolección y entrega de paquetes, documentos y mercancía en toda Bogotá. Servicio el mismo día. Seguimiento en tiempo real por WhatsApp.', 'precio' => 12000, 'categoria' => 'vehiculos', 'subcategoria' => 'mensajeria'],
                ],
                'negocio' => null,
            ],

            // ===== EDUCACIÓN Y TECH =====
            [
                'name' => 'Mónica Salcedo Ureña',
                'email' => 'monica.salcedo.kliksy@example.com',
                'telefono' => '3389012345',
                'perfil' => [
                    'descripcion' => 'Licenciada en matemáticas con experiencia en clases particulares para primaria, bachillerato y pre-universitario. Metodología clara y resultados garantizados.',
                    'experiencia' => '7 años',
                    'ubicacion' => 'Bogotá, Chapinero',
                    'whatsapp' => '3389012345',
                    'categorias' => ['educacion'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Clases de matemáticas y física a domicilio', 'descripcion' => 'Refuerzo escolar y preparación para grado 11, ICFES y pre-universitario. Matemáticas, física y estadística. Metodología práctica con ejercicios reales.', 'precio' => 40000, 'categoria' => 'educacion', 'subcategoria' => 'clases'],
                    ['titulo' => 'Clases de inglés - todos los niveles', 'descripcion' => 'Inglés conversacional, gramática, preparación para exámenes IELTS y TOEFL. Clases presenciales o virtuales. Progreso garantizado o devuelvo tu dinero.', 'precio' => 45000, 'categoria' => 'educacion', 'subcategoria' => 'idiomas'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Cristian Bohórquez Téllez',
                'email' => 'cristian.bohorquez.kliksy@example.com',
                'telefono' => '3390123456',
                'perfil' => [
                    'descripcion' => 'Técnico en sistemas. Soporte técnico a domicilio para PC y laptops: formateo, eliminación de virus, actualización de hardware y configuración de redes.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Suba',
                    'whatsapp' => '3390123456',
                    'categorias' => ['educacion'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Formateo y optimización de PC o laptop', 'descripcion' => 'Formateo completo con instalación de Windows 10/11, drivers y programas esenciales. Backup de datos incluido. PC como nuevo en menos de 2 horas. Bogotá.', 'precio' => 50000, 'categoria' => 'educacion', 'subcategoria' => 'soporte_pc'],
                    ['titulo' => 'Eliminación de virus y optimización', 'descripcion' => 'Limpieza profunda de virus, malware y programas no deseados. Optimización del arranque y rendimiento. Sin necesidad de formatear. Garantía de 30 días.', 'precio' => 35000, 'categoria' => 'educacion', 'subcategoria' => 'soporte_pc'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Felipe Arango Gutiérrez',
                'email' => 'felipe.arango.kliksy@example.com',
                'telefono' => '3401234567',
                'perfil' => [
                    'descripcion' => 'Músico y docente de guitarra y piano. Clases para niños, jóvenes y adultos. Todos los niveles desde cero. Metodología divertida y resultados rápidos.',
                    'experiencia' => '6 años',
                    'ubicacion' => 'Bogotá, Teusaquillo',
                    'whatsapp' => '3401234567',
                    'categorias' => ['educacion'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Clases de guitarra a domicilio', 'descripcion' => 'Aprende guitarra desde cero o mejora tu técnica. Géneros: pop, rock, vallenato y clásico. Clases de 1 hora con material didáctico incluido. Todas las edades.', 'precio' => 50000, 'categoria' => 'educacion', 'subcategoria' => 'musica'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Juliana Castro Peña',
                'email' => 'juliana.castro.kliksy@example.com',
                'telefono' => '3412345678',
                'perfil' => [
                    'descripcion' => 'Fotógrafa profesional para eventos, productos y sesiones a domicilio. Edición incluida. Entrega en 48 horas. Equipos de alta gama.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Usaquén',
                    'whatsapp' => '3412345678',
                    'categorias' => ['educacion'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Sesión fotográfica a domicilio', 'descripcion' => 'Sesión de 2 horas en tu hogar o lugar favorito. Incluye 30 fotografías editadas en alta resolución entregadas en 48 horas. Perfecta para familias, parejas o profesionales.', 'precio' => 200000, 'categoria' => 'educacion', 'subcategoria' => 'fotografia'],
                ],
                'negocio' => null,
            ],

            // ===== MASCOTAS =====
            [
                'name' => 'Andrea Villamil Soto',
                'email' => 'andrea.villamil.kliksy@example.com',
                'telefono' => '3423456789',
                'perfil' => [
                    'descripcion' => 'Veterinaria a domicilio. Consultas, vacunación, desparasitación y primeros auxilios para perros y gatos. Atención en tu hogar sin estrés para tu mascota.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Engativá',
                    'whatsapp' => '3423456789',
                    'categorias' => ['mascotas'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Consulta veterinaria a domicilio', 'descripcion' => 'Consulta médica veterinaria en tu hogar para perros y gatos. Revisión completa, diagnóstico y formulación. Más cómodo para tu mascota y para ti. Bogotá.', 'precio' => 70000, 'categoria' => 'mascotas', 'subcategoria' => 'veterinario'],
                    ['titulo' => 'Vacunación y desparasitación a domicilio', 'descripcion' => 'Plan de vacunación completo para perros y gatos según edad. Desparasitación interna y externa incluida. Cadena de frío garantizada. Certificado de vacunación.', 'precio' => 55000, 'categoria' => 'mascotas', 'subcategoria' => 'veterinario'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Samuel Herrera Pinzón',
                'email' => 'samuel.herrera.kliksy@example.com',
                'telefono' => '3434567890',
                'perfil' => [
                    'descripcion' => 'Groomer canino certificado. Baño, corte y arreglo de mascotas a domicilio. Todos los tamaños y razas. Productos naturales y cariño garantizado.',
                    'experiencia' => '4 años',
                    'ubicacion' => 'Bogotá, Barrios Unidos',
                    'whatsapp' => '3434567890',
                    'categorias' => ['mascotas'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Baño y corte canino a domicilio', 'descripcion' => 'Servicio completo de peluquería canina: baño con shampoo hipoalergénico, secado, corte de pelo, uñas y limpieza de oídos. Llevo todo el equipo. Todas las razas.', 'precio' => 45000, 'categoria' => 'mascotas', 'subcategoria' => 'peluqueria_pet'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Vanessa Medina Guerrero',
                'email' => 'vanessa.medina.kliksy@example.com',
                'telefono' => '3445678901',
                'perfil' => [
                    'descripcion' => 'Paseadora certificada con amor por los animales. Paseos individuales y en grupo, cuidado en casa y guardería. Actualizaciones en tiempo real a los dueños.',
                    'experiencia' => '3 años',
                    'ubicacion' => 'Bogotá, Chapinero',
                    'whatsapp' => '3445678901',
                    'categorias' => ['mascotas'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Paseo de perros - 1 hora diaria', 'descripcion' => 'Paseo individual de 60 minutos. Envío de fotos y ubicación en tiempo real. Máximo 3 perros simultáneos. Zona norte de Bogotá. Descuentos por paquetes semanales.', 'precio' => 18000, 'categoria' => 'mascotas', 'subcategoria' => 'paseador'],
                ],
                'negocio' => null,
            ],

            // ===== CUIDADO DE PERSONAS =====
            [
                'name' => 'Rosa Elena Pinto Valderrama',
                'email' => 'rosa.pinto.kliksy@example.com',
                'telefono' => '3456789012',
                'perfil' => [
                    'descripcion' => 'Niñera con experiencia en cuidado de bebés y niños hasta 12 años. Referencias verificables. Primeros auxilios certificada. Disponibilidad fines de semana.',
                    'experiencia' => '8 años',
                    'ubicacion' => 'Bogotá, Suba',
                    'whatsapp' => '3456789012',
                    'categorias' => ['cuidado'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Cuidado de niños a domicilio', 'descripcion' => 'Cuidado de niños en tu hogar por horas o días. Actividades lúdicas, apoyo en tareas, alimentación y seguridad. Referencias verificables. Fines de semana disponible.', 'precio' => 25000, 'categoria' => 'cuidado', 'subcategoria' => 'ninos'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Carmen Lucía Bernal Fuentes',
                'email' => 'carmen.bernal.kliksy@example.com',
                'telefono' => '3467890123',
                'perfil' => [
                    'descripcion' => 'Auxiliar de enfermería con 12 años de experiencia en cuidado de adultos mayores a domicilio. Manejo de enfermedades crónicas, movilidad reducida y demencia.',
                    'experiencia' => '12 años',
                    'ubicacion' => 'Bogotá, Usaquén',
                    'whatsapp' => '3467890123',
                    'categorias' => ['cuidado'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Cuidado de adultos mayores a domicilio', 'descripcion' => 'Acompañamiento y cuidado integral de adultos mayores. Asistencia en higiene, alimentación, medicamentos y movilidad. Disponibilidad de lunes a sábado. Referencias.', 'precio' => 40000, 'categoria' => 'cuidado', 'subcategoria' => 'adultos_mayores'],
                    ['titulo' => 'Toma de muestras y cuidados de enfermería', 'descripcion' => 'Toma de muestras de sangre, orina y más en tu hogar. Aplicación de inyecciones, curaciones y seguimiento de signos vitales. Auxiliar de enfermería certificada.', 'precio' => 30000, 'categoria' => 'cuidado', 'subcategoria' => 'enfermeria'],
                ],
                'negocio' => null,
            ],

            // ===== GASTRONOMÍA =====
            [
                'name' => 'Jhon Fredy Muñoz Zapata',
                'email' => 'jhonfredy.munoz.kliksy@example.com',
                'telefono' => '3478901234',
                'perfil' => [
                    'descripcion' => 'Chef profesional con experiencia en cocina colombiana e internacional. Cenas románticas, cumpleaños y menús personalizados en tu hogar. Ingredientes frescos garantizados.',
                    'experiencia' => '9 años',
                    'ubicacion' => 'Bogotá, Chicó',
                    'whatsapp' => '3478901234',
                    'categorias' => ['gastronomia'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Chef a domicilio - cena romántica', 'descripcion' => 'Experiencia gastronómica de 3 tiempos en tu hogar. Menú personalizado, ingredientes frescos, decoración de mesa y limpieza al finalizar. Para 2 personas.', 'precio' => 250000, 'categoria' => 'gastronomia', 'subcategoria' => 'chef'],
                    ['titulo' => 'Catering para eventos hasta 50 personas', 'descripcion' => 'Servicio de catering completo para grados, matrimonios, empresas y fiestas. Menú buffet o a la carta. Incluye montaje, servicio y desmontaje. Cotización gratis.', 'precio' => 35000, 'categoria' => 'gastronomia', 'subcategoria' => 'catering'],
                ],
                'negocio' => [
                    'nombre' => 'Sabores JF - Chef a Domicilio',
                    'descripcion' => 'Servicio gastronómico a domicilio especializado en cocina colombiana e internacional. Cenas románticas, catering para eventos y menús corporativos. Bogotá y alrededores.',
                    'direccion' => 'Cra 15 #88-30, Chicó, Bogotá',
                    'telefono' => '3478901234',
                    'categoria' => 'gastronomia',
                ],
            ],
            [
                'name' => 'Marcela Ríos Angulo',
                'email' => 'marcela.rios.kliksy@example.com',
                'telefono' => '3489012345',
                'perfil' => [
                    'descripcion' => 'Repostera artesanal especializada en tortas personalizadas, cupcakes y postres para eventos. Diseños únicos y sabores irresistibles.',
                    'experiencia' => '6 años',
                    'ubicacion' => 'Bogotá, Kennedy',
                    'whatsapp' => '3489012345',
                    'categorias' => ['gastronomia'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Torta personalizada para cumpleaños', 'descripcion' => 'Tortas artesanales decoradas a tu gusto: fondant, buttercream, naked cake y más. Sabores: vainilla, chocolate, red velvet, zanahoria y más. Entrega a domicilio.', 'precio' => 80000, 'categoria' => 'gastronomia', 'subcategoria' => 'reposteria'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Daniel Sánchez Orozco',
                'email' => 'daniel.sanchez.kliksy@example.com',
                'telefono' => '3490123456',
                'perfil' => [
                    'descripcion' => 'Bartender profesional para eventos. Cócteles clásicos y moleculares, open bar y barras de licores. Shows de flair bartending incluidos.',
                    'experiencia' => '7 años',
                    'ubicacion' => 'Bogotá, La Candelaria',
                    'whatsapp' => '3490123456',
                    'categorias' => ['gastronomia'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Bartender para eventos y fiestas', 'descripcion' => 'Servicio completo de bartender para tu evento: cócteles clásicos y de autor, open bar, barra de tragos y show de flair. Incluye utensilios y presentación profesional.', 'precio' => 150000, 'categoria' => 'gastronomia', 'subcategoria' => 'bartender'],
                ],
                'negocio' => null,
            ],

            // ===== DEPORTE Y BIENESTAR =====
            [
                'name' => 'Andrés Felipe Mora Ocampo',
                'email' => 'andres.mora.kliksy@example.com',
                'telefono' => '3501234567',
                'perfil' => [
                    'descripcion' => 'Entrenador personal certificado. Rutinas de fuerza, cardio y pérdida de peso en tu hogar o parque. Planes de nutrición básicos incluidos.',
                    'experiencia' => '5 años',
                    'ubicacion' => 'Bogotá, Palermo',
                    'whatsapp' => '3501234567',
                    'categorias' => ['deporte'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Entrenamiento personal a domicilio', 'descripcion' => 'Sesiones de 1 hora con rutinas personalizadas según tu objetivo: pérdida de peso, tonificación o ganancia muscular. Llevo implementos básicos. Plan mensual recomendado.', 'precio' => 45000, 'categoria' => 'deporte', 'subcategoria' => 'entrenador'],
                    ['titulo' => 'Clases de yoga y meditación en casa', 'descripcion' => 'Sesiones de yoga restaurativo, vinyasa y meditación guiada en tu hogar. Para principiantes y niveles intermedios. Llevo colchoneta y materiales. Bogotá norte.', 'precio' => 40000, 'categoria' => 'deporte', 'subcategoria' => 'yoga'],
                ],
                'negocio' => null,
            ],
            [
                'name' => 'Lina María Suárez Bermúdez',
                'email' => 'lina.suarez.kliksy@example.com',
                'telefono' => '3512345678',
                'perfil' => [
                    'descripcion' => 'Instructora de baile con experiencia en salsa, bachata y danzas urbanas. Clases para parejas, grupos e individuales. ¡Aprende a bailar en tu propio hogar!',
                    'experiencia' => '8 años',
                    'ubicacion' => 'Bogotá, Normandía',
                    'whatsapp' => '3512345678',
                    'categorias' => ['deporte'],
                    'en_vacaciones' => false,
                ],
                'servicios' => [
                    ['titulo' => 'Clases de salsa y bachata a domicilio', 'descripcion' => 'Aprende salsa caleña, salsa casino o bachata desde cero. Clases de 1 hora para parejas o individuales. Progreso garantizado en 4 sesiones. ¡Con música y diversión!', 'precio' => 50000, 'categoria' => 'deporte', 'subcategoria' => 'danza'],
                ],
                'negocio' => null,
            ],
        ];

        // ---------------------------------------------------------------
        // INSERTAR
        // ---------------------------------------------------------------
        foreach ($profesionales as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Kliksy2026*'),
                'telefono' => $data['telefono'],
                'role_id' => 2,
                'onboarding_completado' => true,
            ]);

            $perfil = PerfilProfesional::create([
                'user_id' => $user->id,
                'descripcion' => $data['perfil']['descripcion'],
                'experiencia' => $data['perfil']['experiencia'],
                'ubicacion' => $data['perfil']['ubicacion'],
                'whatsapp' => $data['perfil']['whatsapp'],
                'categorias' => json_encode($data['perfil']['categorias']),
                'en_vacaciones' => $data['perfil']['en_vacaciones'],
            ]);

            foreach ($data['servicios'] as $s) {
                Servicio::create([
                    'user_id' => $user->id,
                    'titulo' => $s['titulo'],
                    'descripcion' => $s['descripcion'],
                    'precio' => $s['precio'],
                    'categoria' => $s['categoria'],
                    'subcategoria' => $s['subcategoria'],
                ]);
            }

            if (! empty($data['negocio'])) {
                Negocio::create([
                    'perfil_profesional_id' => $perfil->id,
                    'nombre' => $data['negocio']['nombre'],
                    'descripcion' => $data['negocio']['descripcion'],
                    'direccion' => $data['negocio']['direccion'],
                    'telefono' => $data['negocio']['telefono'],
                    'categoria' => $data['negocio']['categoria'],
                ]);
            }
        }
    }
}
