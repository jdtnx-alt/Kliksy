<?php

namespace App\Http\Controllers;

use App\Helpers\PalabraProhibidaHelper;
use App\Models\Resena;
use App\Models\RespuestaResena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespuestaResenaController extends Controller
{
    public function store(Request $request, $resenaId)
    {
        $request->validate([
            'contenido' => 'required|string|max:500',
        ]);

        // Filtro de palabras prohibidas
        if (PalabraProhibidaHelper::contiene($request->contenido)) {
            return back()
                ->withInput()
                ->with('error', 'Tu respuesta contiene palabras inapropiadas. Por favor revisa el contenido e inténtalo de nuevo.');
        }

        $resena = Resena::findOrFail($resenaId);

        // Solo el profesional o el cliente de la reseña pueden responder
        if (Auth::id() !== $resena->profesional_id && Auth::id() !== $resena->cliente_id) {
            abort(403);
        }

        RespuestaResena::create([
            'resena_id' => $resenaId,
            'user_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);

        return back()->with('success', 'Respuesta enviada.');
    }
}
