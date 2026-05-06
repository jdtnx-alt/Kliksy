<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'required|string|max:20|unique:users,telefono',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:cliente,profesional',
        ]);

        $roleId = $request->role === 'cliente' ? 1 : 2;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'role_id' => $roleId,
            'password' => Hash::make($request->password),
        ]);

        auth()->login($user);

        Mail::to($user->email)->send(new BienvenidaMail($user->name));

        if ($user->role_id === 2) {
            return redirect()->route('profesional.onboarding');
        }

        return redirect()->route('inicio');
    }
}
