<?php

namespace App\Http\Controllers;

use App\Models\FotoPerfil;
use App\Models\PerfilProfesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FotoPerfilController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fotos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $perfil = PerfilProfesional::where('user_id', Auth::id())->firstOrFail();

        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('fotos_perfil', 'public');
            FotoPerfil::create([
                'perfil_profesional_id' => $perfil->id,
                'ruta' => $ruta,
            ]);
        }

        return back()->with('success', 'Fotos subidas correctamente');
    }

    public function destroy($id)
    {
        $foto = FotoPerfil::findOrFail($id);

        $perfil = PerfilProfesional::where('user_id', Auth::id())->firstOrFail();

        if ($foto->perfil_profesional_id !== $perfil->id) {
            abort(403);
        }

        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();

        return back()->with('success', 'Foto eliminada');
    }
}
