<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ProfesionalServicioController extends Controller
{
    public function store(Request $request)
    {
        if (! auth()->user()->hasVerifiedEmail() && ! str_contains(url()->previous(), 'onboarding')) {
            return redirect()->back()->with('error', 'Debes verificar tu correo electrónico antes de publicar servicios.');
        }

        $fotosRutas = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $fotosRutas[] = $foto->store('servicios', 'public');
            }
        }

        Servicio::create([
            'user_id' => auth()->id(),
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'subcategoria' => $request->subcategoria,
            'duracion' => $request->duracion ?? 60,
            'foto' => $fotosRutas[0] ?? null,
            'fotos' => $fotosRutas ?: null,
        ]);

        return redirect()->route('profesional.onboarding')
            ->with('success', 'Servicio publicado correctamente.')
            ->with('paso_completado', 'servicio');
    }
}
