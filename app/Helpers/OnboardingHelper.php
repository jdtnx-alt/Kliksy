<?php

namespace App\Helpers;

use App\Models\User;

class OnboardingHelper
{
    public static function pasos(User $user): array
    {
        $perfil = $user->perfilProfesional;
        $servicios = $user->servicios;
        $fotos = $perfil?->fotos ?? collect();
        $negocio = $perfil?->negocio;

        return [
            [
                'id' => 'cedula',
                'titulo' => 'Verifica tu identidad',
                'descripcion' => 'Sube la foto delantera y trasera de tu cédula',
                'icono' => 'bi-shield-check',
                'url' => route('profesional.onboarding'),
                'completado' => $perfil?->cedula_frontal && $perfil?->cedula_trasera,
                'requerido' => true,
            ],
            [
                'id' => 'perfil',
                'titulo' => 'Completa tu perfil',
                'descripcion' => 'Agrega tu descripción, experiencia, ubicación y WhatsApp',
                'icono' => 'bi-person',
                'url' => route('profesional.onboarding'),
                'completado' => $perfil &&
                                 $perfil->descripcion &&
                                 $perfil->whatsapp &&
                                 $perfil->ubicacion,
                'requerido' => true,
            ],
            [
                'id' => 'servicio',
                'titulo' => 'Publica tu primer servicio',
                'descripcion' => 'Agrega al menos un servicio con título, descripción y precio',
                'icono' => 'bi-tools',
                'url' => route('profesional.onboarding'),
                'completado' => $servicios->count() > 0,
                'requerido' => true,
            ],
            [
                'id' => 'foto',
                'titulo' => 'Sube fotos de tus trabajos',
                'descripcion' => 'Las fotos aumentan un 60% tus probabilidades de ser contactado',
                'icono' => 'bi-camera',
                'url' => route('profesional.onboarding'),
                'completado' => $fotos->count() > 0,
                'requerido' => true,
            ],
            [
                'id' => 'negocio',
                'titulo' => 'Registra tu negocio físico',
                'descripcion' => 'Opcional: si tienes un local, agrégalo para más visibilidad',
                'icono' => 'bi-shop',
                'url' => route('profesional.onboarding'),
                'completado' => $negocio !== null,
                'requerido' => false,
            ],
        ];
    }

    public static function progreso(User $user): int
    {
        $pasos = self::pasos($user);
        $completados = collect($pasos)->where('completado', true)->count();

        return (int) round(($completados / count($pasos)) * 100);
    }

    public static function perfilActivo(User $user): bool
    {
        $perfil = $user->perfilProfesional;

        return $perfil &&
               $perfil->cedula_frontal &&
               $perfil->cedula_trasera &&
               $perfil->descripcion &&
               $perfil->whatsapp &&
               $user->servicios->count() > 0 &&
               $user->perfilProfesional->fotos->count() > 0;
    }
}
