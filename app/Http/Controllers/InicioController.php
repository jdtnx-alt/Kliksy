<?php

namespace App\Http\Controllers;

use App\Models\User;

class InicioController extends Controller
{
    public function index()
    {
        $profesionales = User::where('role_id', 2)
            ->with([
                'resenas',
                'servicios',
                'perfilProfesional',
                'perfilProfesional.fotos',
            ])
            ->get()
            ->filter(function ($user) {
                $perfil = $user->perfilProfesional;

                return $perfil &&
                       $perfil->descripcion &&
                       $perfil->whatsapp &&
                       $user->servicios->count() > 0 &&
                       $perfil->fotos->count() > 0;
            })
            ->sortByDesc(function ($user) {
                $perfil = $user->perfilProfesional;
                $resenas = $user->resenas->count();
                $promedio = $user->promedioCalificacion();
                $completados = \App\Models\Solicitud::where('profesional_id', $user->id)
                    ->where('estado', 'completada')->count();
                $verificado = ($perfil?->cedula_frontal && $perfil?->cedula_trasera) ? 20 : 0;

                return ($resenas * 2) + ($promedio * 5) + $completados + $verificado;
            })
            ->take(6)
            ->values();

        return view('inicio', compact('profesionales'));
    }
}
