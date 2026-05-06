<?php

namespace App\Http\Controllers;

use App\Models\PerfilProfesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilProfesionalController extends Controller
{
    public function guardar(Request $request)
    {
        $request->validate([
            'descripcion' => 'nullable|string',
            'experiencia' => 'nullable|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'categorias' => 'nullable|array',
            'duracion_promedio' => 'nullable|integer',
            'dias_laborables' => 'nullable|array',
        ]);

        // Fix to handle unselected checkboxes which send nothing
        $diasLaborables = $request->has('dias_laborables') ? $request->dias_laborables : [];

        PerfilProfesional::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'descripcion' => $request->descripcion,
                'experiencia' => $request->experiencia,
                'ubicacion' => $request->ubicacion,
                'whatsapp' => $request->whatsapp,
                'categorias' => $request->categorias,
                'duracion_promedio' => $request->duracion_promedio,
                'dias_laborables' => $diasLaborables,
            ]
        );

        return redirect()->route('profesional.perfil')
            ->with('success', 'Perfil guardado correctamente.');
    }

    public function guardarCedula(Request $request)
    {
        $request->validate([
            'cedula_frontal' => 'nullable|image|max:5120',
            'cedula_trasera' => 'nullable|image|max:5120',
        ]);

        $perfil = auth()->user()->perfilProfesional
            ?? \App\Models\PerfilProfesional::create(['user_id' => auth()->id()]);

        if ($request->hasFile('cedula_frontal')) {
            if ($perfil->cedula_frontal) {
                \Storage::disk('private')->delete($perfil->cedula_frontal);
            }
            $perfil->cedula_frontal = $request->file('cedula_frontal')
                ->store('cedulas', 'private');
        }

        if ($request->hasFile('cedula_trasera')) {
            if ($perfil->cedula_trasera) {
                \Storage::disk('private')->delete($perfil->cedula_trasera);
            }
            $perfil->cedula_trasera = $request->file('cedula_trasera')
                ->store('cedulas', 'private');
        }

        $perfil->save();

        return redirect()->route('profesional.onboarding')
            ->with('success', 'Cédula guardada correctamente.')
            ->with('paso_completado', 'cedula');
    }
}
