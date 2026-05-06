<?php

namespace App\Helpers;

class CategoriaHelper
{
    /**
     * Árbol completo: categorías padre con sus subcategorías.
     * slug        → valor guardado en DB
     * nombre      → texto visible
     * icono       → Bootstrap Icon
     */
    public static function arbol(): array
    {
        return [
            'belleza' => [
                'nombre' => 'Belleza personal',
                'icono' => 'bi-stars',
                'color' => 'pink',
                'subs' => [
                    'barberia' => 'Barbería',
                    'peluqueria' => 'Peluquería femenina',
                    'manicura' => 'Manicura y pedicura',
                    'maquillaje' => 'Maquillaje',
                    'depilacion' => 'Depilación',
                    'spa_domicilio' => 'Spa a domicilio',
                    'pestanas' => 'Extensión de pestañas',
                    'tatuajes' => 'Tatuajes a domicilio',
                ],
            ],
            'hogar' => [
                'nombre' => 'Hogar',
                'icono' => 'bi-house-heart',
                'color' => 'blue',
                'subs' => [
                    'aseo' => 'Aseo del hogar',
                    'pintura' => 'Pintura',
                    'electrodomesticos' => 'Electrodomésticos',
                    'mudanzas' => 'Mudanzas',
                    'montaje' => 'Montaje de muebles',
                    'fumigacion' => 'Fumigación y plagas',
                    'jardineria' => 'Jardinería',
                    'cerrajeria' => 'Cerrajería',
                    'impermeabil' => 'Impermeabilización',
                    'lavado_alfombra' => 'Lavado de alfombras',
                ],
            ],
            'instalaciones' => [
                'nombre' => 'Instalaciones',
                'icono' => 'bi-lightning-charge',
                'color' => 'yellow',
                'subs' => [
                    'electricidad' => 'Electricidad',
                    'plomeria' => 'Plomería',
                    'gas' => 'Gas natural',
                    'aires' => 'Aires acondicionados',
                    'alarmas' => 'Cámaras y alarmas',
                    'redes' => 'Redes e internet',
                    'soldadura' => 'Soldadura',
                ],
            ],
            'vehiculos' => [
                'nombre' => 'Vehículos',
                'icono' => 'bi-car-front',
                'color' => 'green',
                'subs' => [
                    'lavado_auto' => 'Lavado de autos',
                    'mecanica' => 'Mecánica a domicilio',
                    'llantas' => 'Llantas y balanceo',
                    'mensajeria' => 'Domicilios y encomiendas',
                ],
            ],
            'educacion' => [
                'nombre' => 'Educación y tech',
                'icono' => 'bi-mortarboard',
                'color' => 'indigo',
                'subs' => [
                    'clases' => 'Clases particulares',
                    'idiomas' => 'Idiomas',
                    'musica' => 'Clases de música',
                    'soporte_pc' => 'Soporte técnico PC',
                    'fotografia' => 'Fotografía a domicilio',
                ],
            ],
            'mascotas' => [
                'nombre' => 'Mascotas',
                'icono' => 'bi-heart',
                'color' => 'orange',
                'subs' => [
                    'veterinario' => 'Veterinario a domicilio',
                    'peluqueria_pet' => 'Peluquería canina',
                    'paseador' => 'Paseo de mascotas',
                ],
            ],
            'cuidado' => [
                'nombre' => 'Cuidado de personas',
                'icono' => 'bi-person-heart',
                'color' => 'red',
                'subs' => [
                    'ninos' => 'Cuidado de niños',
                    'adultos_mayores' => 'Adultos mayores',
                    'enfermeria' => 'Enfermería a domicilio',
                ],
            ],
            'gastronomia' => [
                'nombre' => 'Gastronomía',
                'icono' => 'bi-cup-hot',
                'color' => 'amber',
                'subs' => [
                    'chef' => 'Chef a domicilio',
                    'catering' => 'Catering a domicilio',
                    'bartender' => 'Bartender',
                    'reposteria' => 'Repostería y tortas',
                ],
            ],
            'deporte' => [
                'nombre' => 'Deporte y bienestar',
                'icono' => 'bi-activity',
                'color' => 'teal',
                'subs' => [
                    'entrenador' => 'Entrenador personal',
                    'yoga' => 'Yoga y pilates',
                    'danza' => 'Clases de baile',
                ],
            ],
        ];
    }

    /** Devuelve solo los padres: slug => nombre */
    public static function padres(): array
    {
        return collect(self::arbol())
            ->map(fn ($v) => $v['nombre'])
            ->toArray();
    }

    /** Devuelve todas las subcategorías aplanadas: slug => nombre */
    public static function todasLasSubs(): array
    {
        $result = [];
        foreach (self::arbol() as $padre) {
            foreach ($padre['subs'] as $slug => $nombre) {
                $result[$slug] = $nombre;
            }
        }

        return $result;
    }

    /** Dado un slug de sub, devuelve el slug padre */
    public static function padreDeSub(string $subSlug): ?string
    {
        foreach (self::arbol() as $padreSlug => $padre) {
            if (array_key_exists($subSlug, $padre['subs'])) {
                return $padreSlug;
            }
        }

        return null;
    }

    /** Nombre legible de cualquier slug (padre o sub) */
    public static function nombre(string $slug): string
    {
        $arbol = self::arbol();
        if (isset($arbol[$slug])) {
            return $arbol[$slug]['nombre'];
        }
        foreach ($arbol as $padre) {
            if (isset($padre['subs'][$slug])) {
                return $padre['subs'][$slug];
            }
        }

        return ucfirst($slug);
    }

    public static function sinonimos(): array
    {
        return [
            // Barbería
            'barberia' => ['peluquero', 'barbero', 'corte', 'pelo', 'cabello', 'afeitado', 'barba', 'degradado', 'rapado'],
            // Peluquería
            'peluqueria' => ['tinte', 'decoloración', 'alaciado', 'keratina', 'ondas', 'permanente', 'mechas', 'balayage'],
            // Manicura
            'manicura' => ['uñas', 'acrílicas', 'gelish', 'semipermanente', 'pedicura', 'pies', 'manicurista'],
            // Maquillaje
            'maquillaje' => ['maquilladora', 'maquillista', 'novia', 'quince', 'caracterización', 'pestañas postizas'],
            // Depilación
            'depilacion' => ['cera', 'vello', 'hilo', 'laser', 'depilar', 'depiladora'],
            // Spa
            'spa_domicilio' => ['masaje', 'relajación', 'aromaterapia', 'facial', 'spa', 'masajista', 'relajante'],
            // Pestañas
            'pestanas' => ['extensión pestañas', 'lifting', 'rimel', 'pelo a pelo', 'volumen ruso'],
            // Tatuajes
            'tatuajes' => ['tatuador', 'piercing', 'tinta', 'tattoo', 'perforación'],
            // Aseo hogar
            'aseo' => ['limpieza', 'limpiar', 'aseo', 'empleada', 'doméstica', 'orden', 'barrer', 'trapear', 'sacudir'],
            // Pintura
            'pintura' => ['pintar', 'pintor', 'pared', 'estuco', 'fachada', 'acabados', 'rodillo', 'brocha'],
            // Electrodomésticos
            'electrodomesticos' => ['lavadora', 'nevera', 'heladera', 'refrigerador', 'microondas', 'estufa', 'secadora', 'técnico', 'reparar', 'arreglar electrodoméstico'],
            // Mudanzas
            'mudanzas' => ['mudanza', 'trasteo', 'mover', 'cargar muebles', 'flete', 'camión', 'transporte muebles'],
            // Montaje muebles
            'montaje' => ['armar', 'ensamblar', 'ikea', 'closet', 'estante', 'escritorio', 'mueble'],
            // Fumigación
            'fumigacion' => ['plagas', 'cucarachas', 'ratas', 'ratones', 'hormigas', 'termitas', 'fumigador', 'insectos', 'bichos'],
            // Jardinería
            'jardineria' => ['jardín', 'poda', 'plantas', 'césped', 'grama', 'sembrar', 'jardinero', 'regar'],
            // Cerrajería
            'cerrajeria' => ['cerradura', 'chapa', 'llave', 'puerta', 'candado', 'cerrajero', 'abrir puerta'],
            // Impermeabilización
            'impermeabil' => ['filtración', 'gotera', 'humedad', 'techo', 'terraza', 'impermeabilizar', 'agua lluvia'],
            // Lavado alfombras
            'lavado_alfombra' => ['alfombra', 'tapete', 'sofá', 'sillón', 'tapizado', 'muebles lavado'],
            // Electricidad
            'electricidad' => ['electricista', 'luz', 'cable', 'enchufe', 'tomacorriente', 'corto', 'apagón', 'instalación eléctrica', 'voltaje', 'breaker'],
            // Plomería
            'plomeria' => ['plomero', 'fontanero', 'tubería', 'tubo', 'gotera', 'fuga', 'agua', 'desagüe', 'destape', 'llave de paso', 'sanitario', 'inodoro'],
            // Gas
            'gas' => ['gas natural', 'propano', 'calentador', 'estufa gas', 'tubería gas', 'gasoducto'],
            // Aires
            'aires' => ['aire acondicionado', 'clima', 'frío', 'calor', 'split', 'minisplit', 'ventilación', 'refrigeración'],
            // Alarmas
            'alarmas' => ['cámara', 'cctv', 'seguridad', 'alarma', 'sensor', 'intercomunicador', 'vigilancia', 'videoportero'],
            // Redes
            'redes' => ['internet', 'wifi', 'red', 'router', 'cable red', 'fibra óptica', 'lan', 'conectividad'],
            // Soldadura
            'soldadura' => ['soldar', 'soldador', 'reja', 'portón', 'escalera', 'estructura metálica', 'hierro', 'acero'],
            // Lavado autos
            'lavado_auto' => ['lavar carro', 'lavar moto', 'lavado auto', 'encerado', 'detailing', 'pulir carro', 'limpieza vehiculo'],
            // Mecánica
            'mecanica' => ['mecánico', 'aceite', 'frenos', 'batería', 'llanta pinchada', 'carro dañado', 'motor', 'diagnóstico'],
            // Llantas
            'llantas' => ['llanta', 'neumático', 'alineación', 'balanceo', 'caucho', 'pinchada'],
            // Domicilios
            'mensajeria' => ['domicilio', 'encomienda', 'paquete', 'envío', 'mensajero', 'moto', 'delivery'],
            // Clases
            'clases' => ['profesor', 'tutor', 'tutoría', 'matemáticas', 'física', 'química', 'inglés colegio', 'refuerzo', 'tareas'],
            // Idiomas
            'idiomas' => ['inglés', 'francés', 'portugués', 'alemán', 'idioma', 'lengua', 'conversación'],
            // Música
            'musica' => ['guitarra', 'piano', 'batería', 'canto', 'violín', 'música', 'instrumento'],
            // Soporte PC
            'soporte_pc' => ['computador', 'pc', 'laptop', 'virus', 'formatear', 'windows', 'pantalla azul', 'internet lento', 'técnico computadores'],
            // Fotografía
            'fotografia' => ['fotógrafo', 'foto', 'video', 'evento', 'sesión', 'retratos', 'producto', 'fotografía'],
            // Veterinario
            'veterinario' => ['veterinario', 'vacuna', 'perro enfermo', 'gato enfermo', 'animal', 'mascota enferma', 'consulta veterinaria'],
            // Peluquería canina
            'peluqueria_pet' => ['baño perro', 'corte perro', 'peluquería canina', 'grooming', 'mascota baño'],
            // Paseador
            'paseador' => ['pasear perro', 'cuidar mascota', 'pensión mascotas', 'dog walker'],
            // Niños
            'ninos' => ['niñera', 'cuidar niños', 'babysitter', 'guardería', 'niños', 'bebé'],
            // Adultos mayores
            'adultos_mayores' => ['abuelo', 'anciano', 'adulto mayor', 'tercera edad', 'acompañante', 'cuidado mayor'],
            // Enfermería
            'enfermeria' => ['enfermera', 'inyección', 'curación', 'herida', 'suero', 'toma de muestras', 'tensión', 'glucosa'],
            // Chef
            'chef' => ['cocinero', 'chef', 'cocinar', 'comida', 'menú', 'cena romántica', 'almuerzo'],
            // Catering
            'catering' => ['evento', 'grado', 'matrimonio', 'fiesta', 'banquete', 'catering', 'comida evento'],
            // Bartender
            'bartender' => ['cocteles', 'bar', 'bebidas', 'trago', 'open bar', 'bartender', 'cóctel'],
            // Repostería
            'reposteria' => ['torta', 'ponqué', 'pastel', 'cupcake', 'postre', 'repostería', 'cumpleaños', 'decorar torta'],
            // Entrenador
            'entrenador' => ['entrenador', 'gym en casa', 'ejercicio', 'rutina', 'fitness', 'pesas', 'cardio', 'bajar de peso'],
            // Yoga
            'yoga' => ['yoga', 'pilates', 'meditación', 'respiración', 'flexibilidad', 'mindfulness'],
            // Danza
            'danza' => ['baile', 'salsa', 'bachata', 'urbano', 'danza', 'bailar', 'coreografía'],
        ];
    }
}
