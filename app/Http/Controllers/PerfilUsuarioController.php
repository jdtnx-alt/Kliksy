<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilUsuarioController extends Controller
{
    public function index()
    {
        return view('perfil.index');
    }

    public function actualizar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'telefono' => 'required|string|max:20|unique:users,telefono,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ]);

        return back()->with('success', 'Información actualizada correctamente');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->password_actual, Auth::user()->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}
