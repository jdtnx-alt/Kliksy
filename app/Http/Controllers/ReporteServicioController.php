<?php

namespace App\Http\Controllers;

use App\Models\ReporteServicio;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;

class ReporteServicioController extends Controller
{
    /**
     * Reportar un servicio específico
     */
    public function storeServicio(Request $request, $servicioId)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $servicio = Servicio::findOrFail($servicioId);

        // No puede reportar sus propios servicios
        if ($servicio->user_id === auth()->id()) {
            return back()->with('error', 'No puedes reportar tus propios servicios.');
        }

        // Evitar reportes duplicados
        $yaReporto = ReporteServicio::where('servicio_id', $servicioId)
            ->where('user_id', auth()->id())
            ->where('tipo', 'servicio')
            ->exists();

        if ($yaReporto) {
            return back()->with('error', 'Ya reportaste este servicio.');
        }

        ReporteServicio::create([
            'servicio_id' => $servicioId,
            'profesional_id' => $servicio->user_id,
            'user_id' => auth()->id(),
            'motivo' => $request->motivo,
            'tipo' => 'servicio',
        ]);

        return back()->with('success', 'Servicio reportado correctamente. Lo revisaremos pronto.');
    }

    /**
     * Reportar un profesional en general
     */
    public function storeProfesional(Request $request, $profesionalId)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $profesional = User::findOrFail($profesionalId);

        // No puede reportarse a sí mismo
        if ($profesional->id === auth()->id()) {
            return back()->with('error', 'No puedes reportarte a ti mismo.');
        }

        // Evitar reportes duplicados
        $yaReporto = ReporteServicio::where('profesional_id', $profesionalId)
            ->where('user_id', auth()->id())
            ->where('tipo', 'profesional')
            ->exists();

        if ($yaReporto) {
            return back()->with('error', 'Ya reportaste a este profesional.');
        }

        ReporteServicio::create([
            'profesional_id' => $profesionalId,
            'user_id' => auth()->id(),
            'motivo' => $request->motivo,
            'tipo' => 'profesional',
        ]);

        return back()->with('success', 'Profesional reportado correctamente. Lo revisaremos pronto.');
    }
}
