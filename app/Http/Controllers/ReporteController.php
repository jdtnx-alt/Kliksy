<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Resena;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function store(Request $request, $resenaId)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $resena = Resena::findOrFail($resenaId);
        $userId = auth()->id();

        // Cliente no puede reportar su propia reseña
        if (auth()->user()->role_id === 1 && $resena->cliente_id === $userId) {
            return back()->with('error', 'No puedes reportar tu propia reseña.');
        }

        // Profesional solo puede reportar reseñas de su perfil
        if (auth()->user()->role_id === 2 && $resena->profesional_id !== $userId) {
            return back()->with('error', 'Solo puedes reportar reseñas en tu perfil.');
        }

        // Evitar reportes duplicados
        $yaReporto = Reporte::where('resena_id', $resenaId)
            ->where('user_id', $userId)
            ->exists();

        if ($yaReporto) {
            return back()->with('error', 'Ya reportaste esta reseña.');
        }

        Reporte::create([
            'resena_id' => $resenaId,
            'user_id' => $userId,
            'motivo' => $request->motivo,
        ]);

        return back()->with('success', 'Reseña reportada correctamente.');
    }
}
