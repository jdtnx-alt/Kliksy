<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\PerfilProfesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegocioController extends Controller
{
    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'categoria' => 'nullable|string',
        ]);

        $perfil = PerfilProfesional::where('user_id', Auth::id())->firstOrFail();

        Negocio::updateOrCreate(
            ['perfil_profesional_id' => $perfil->id],
            [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'categoria' => $request->categoria,
            ]
        );

        return redirect()->route('profesional.info')
            ->with('success', 'Negocio guardado correctamente.');
    }
}
