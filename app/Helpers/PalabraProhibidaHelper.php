<?php

namespace App\Helpers;

class PalabraProhibidaHelper
{
    /**
     * Lista de palabras e insultos prohibidos en reseñas y respuestas.
     * Se buscan como substrings (detecta variaciones con tildes, plurales, etc).
     */
    public static function lista(): array
    {
        return [
            // Insultos directos
            'hijueputa', 'hijuepucha', 'hp', 'h.p', 'malparido', 'malparida',
            'gonorrea', 'gonorrhea', 'maldito', 'maldita', 'imbecil', 'imbécil',
            'estupido', 'estúpido', 'estupida', 'estúpida', 'idiota', 'idiote',
            'pendejo', 'pendeja', 'pendejos', 'pendejas',
            'marica', 'maricon', 'maricón', 'puta', 'puto', 'putas', 'putos',
            'mierda', 'mierdas', 'mierd4', 'mrd', 'hdp',
            'culero', 'culera', 'culo', 'culos',
            'verga', 'vergas', 'vergon', 'vergona',
            'chinga', 'chingado', 'chingada', 'chingas',
            'cabron', 'cabrón', 'cabrona', 'cabrones',
            'zorra', 'zorras', 'zorron', 'zorrón',
            'bastardo', 'bastarda', 'bastardos',
            'animal', 'bestia', 'bruto', 'bruta',
            'ladron', 'ladrón', 'ladrona', 'ladrones',
            'estafador', 'estafadora', 'estafadores',
            'mentiroso', 'mentirosa', 'mentirosos',
            'inutil', 'inútil', 'inutiles', 'inútiles',
            'retrasado', 'retrasada', 'retardado', 'retardada',
            'subnormal', 'anormal',
            'asesino', 'asesina', 'criminal',
            'feo', 'fea', 'gordo', 'gorda', 'gordit',
            // Variaciones con números
            'put4', 'put@', 'p3nd3j', 'c4br0n', 'h1jueputa',
        ];
    }

    /**
     * Retorna true si el texto contiene alguna palabra prohibida.
     */
    public static function contiene(string $texto): bool
    {
        $textoLower = strtolower($texto);
        // Normalizar: quitar tildes para comparación
        $textoNorm = self::normalizarTexto($textoLower);

        foreach (self::lista() as $palabra) {
            $palabraNorm = self::normalizarTexto(strtolower($palabra));
            if (str_contains($textoNorm, $palabraNorm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normaliza tildes y caracteres especiales para comparación.
     */
    private static function normalizarTexto(string $texto): string
    {
        $from = ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'à', 'è', 'ì', 'ò', 'ù'];
        $to = ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u'];

        return str_replace($from, $to, $texto);
    }
}
