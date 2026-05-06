<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas',
            ]);
        }

        // 🔹 Regenera sesión para seguridad
        $request->session()->regenerate();

        // 🔹 Mensaje flash al iniciar sesión
        $request->session()->regenerate();

        $role = Auth::user()->role_id;

        if ($role === 3) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 2) {
            return redirect()->route('profesional.dashboard');
        }

        return redirect('/')->with('success', '¡Has iniciado sesión correctamente!');
    }
}
