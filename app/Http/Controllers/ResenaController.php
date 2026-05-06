<?php

namespace App\Http\Controllers;

use App\Helpers\PalabraProhibidaHelper;
use App\Models\Resena;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResenaController extends Controller
{
    public function store(Request $request, $profesionalId)
    {
        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:500',
        ]);

        // Filtro de palabras prohibidas
        if (PalabraProhibidaHelper::contiene($request->comentario)) {
            return back()
                ->withInput()
                ->with('error', 'Tu reseña contiene palabras inapropiadas. Por favor revisa el contenido e inténtalo de nuevo.');
        }

        // Verificar solicitud completada
        $solicitudCompletada = Solicitud::where('cliente_id', Auth::id())
            ->where('profesional_id', $profesionalId)
            ->where('estado', 'completada')
            ->exists();

        // Verificar reserva completada si el modelo existe
        $reservaCompletada = false;
        if (class_exists(\App\Models\Reserva::class)) {
            $reservaCompletada = \App\Models\Reserva::where('cliente_id', Auth::id())
                ->where('profesional_id', $profesionalId)
                ->where('estado', 'completada')
                ->exists();
        }

        if (! $solicitudCompletada && ! $reservaCompletada) {
            return back()->with('error', 'Solo puedes dejar reseña si el profesional completó tu servicio.');
        }

        // Verificar que no haya dejado reseña antes
        $yaReseno = Resena::where('cliente_id', Auth::id())
            ->where('profesional_id', $profesionalId)
            ->exists();

        if ($yaReseno) {
            return back()->with('error', 'Ya dejaste una reseña a este profesional.');
        }

        Resena::create([
            'servicio_id' => $request->servicio_id,
            'cliente_id' => Auth::id(),
            'profesional_id' => $profesionalId,
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
        ]);

        return back()->with('success', 'Reseña enviada correctamente.');
    }
}
