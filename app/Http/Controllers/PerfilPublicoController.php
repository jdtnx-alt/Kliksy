<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PerfilPublicoController extends Controller
{
    public function show($id)
    {
        $usuario = User::with([
            'perfilProfesional.fotos',
            'perfilProfesional.negocio',
            'servicios',
            'resenas.cliente',
            'resenas.respuestas.user',
        ])->findOrFail($id);

        $puedeResenar = false;
        $yaReseno = false;
        $servicioCompletado = null;

        if (Auth::check() && Auth::user()->role_id === 1) {
            // Verificar solicitud completada (sistema viejo)
            $solicitudCompletada = Solicitud::where('cliente_id', Auth::id())
                ->where('profesional_id', $id)
                ->where('estado', 'completada')
                ->first();

            // Verificar reserva completada (sistema nuevo)
            $reservaCompletada = \App\Models\Reserva::where('cliente_id', Auth::id())
                ->where('profesional_id', $id)
                ->where('estado', 'completada')
                ->first();

            $yaReseno = Resena::where('cliente_id', Auth::id())
                ->where('profesional_id', $id)
                ->exists();

            $puedeResenar = ($solicitudCompletada !== null || $reservaCompletada !== null) && ! $yaReseno;

            // Preferir servicio de la reserva si existe
            $servicioCompletado = $reservaCompletada?->servicio_id ?? $solicitudCompletada?->servicio_id;
        }

        $promedio = $usuario->resenas->count()
            ? round($usuario->resenas->avg('calificacion'), 1)
            : 0;

        return view('profesional.publico', compact(
            'usuario',
            'puedeResenar',
            'yaReseno',
            'servicioCompletado',
            'promedio'
        ));
    }
}
