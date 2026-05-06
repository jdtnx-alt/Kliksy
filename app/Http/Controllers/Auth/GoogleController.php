<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('inicio')->with('openLogin', true);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update(['google_id' => $googleUser->getId()]);
            Auth::login($user, true);

            return redirect()->route('inicio');
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(\Illuminate\Support\Str::random(24)),
                'role_id' => 1,
            ]);

            Mail::to($user->email)->send(new BienvenidaMail($user->name));
            Auth::login($user, true);
            session(['google_usuario_nuevo' => true]);

            return redirect()->route('auth.google.rol');
        }
    }
}
