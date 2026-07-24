<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FotoPerfilController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\PerfilProfesionalController;
use App\Http\Controllers\PerfilPublicoController;
use App\Http\Controllers\PerfilUsuarioController;
use App\Http\Controllers\ProfesionalServicioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReporteServicioController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\RespuestaResenaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// --------------------
// RUTAS PÚBLICAS
// --------------------

Route::get('/', [InicioController::class, 'index'])->name('inicio');
// Verificación de correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('inicio')->with('success', '¡Correo verificado correctamente!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    $redirectTo = $request->user()->role_id === 2
    ? route('profesional.dashboard')
    : route('inicio');

    return redirect($redirectTo)->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
// Google Login
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/rol', function () {
    if (! session('google_usuario_nuevo')) {
        return redirect()->route('inicio');
    }

    return view('auth.google-rol');
})->name('auth.google.rol')->middleware('auth');

Route::post('/auth/google/rol', function (\Illuminate\Http\Request $request) {
    $request->validate(['role' => 'required|in:cliente,profesional']);
    $roleId = $request->role === 'profesional' ? 2 : 1;
    auth()->user()->update(['role_id' => $roleId]);
    session()->forget('google_usuario_nuevo');
    if ($roleId === 2) {
        return redirect()->route('profesional.onboarding');
    }

    return redirect()->route('inicio');
})->name('auth.google.rol.guardar')->middleware('auth');
Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
Route::view('/register', 'auth.register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', function () {
    return redirect()->route('inicio')->with('openLogin', true);
})->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/resenas/{id}/reportar', [ReporteController::class, 'store'])->middleware('auth')->name('resenas.reportar');
Route::post('/servicios/{id}/reportar', [ReporteServicioController::class, 'storeServicio'])
    ->middleware('auth')
    ->name('servicios.reportar');

Route::post('/profesional/{id}/reportar', [ReporteServicioController::class, 'storeProfesional'])
    ->middleware('auth')
    ->name('profesional.reportar');
// Recuperar contraseña
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    Password::sendResetLink($request->only('email'));

    return back()->with('status', 'Te enviamos un enlace de recuperación si el correo está registrado.');
})->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
            ])->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('inicio')->with('status', '¡Contraseña actualizada! Ya puedes iniciar sesión.');
    }

    return back()->withErrors(['email' => __($status)]);
})->name('password.update');

// ⚠️ Rutas específicas de /profesional ANTES de la dinámica {id}
Route::get('/profesional/solicitudes', [SolicitudController::class, 'indexProfesional'])
    ->middleware('auth')->name('profesional.solicitudes');

Route::get('/profesional/info', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }
    $servicios = \App\Models\Servicio::where('user_id', auth()->id())->get();
    $perfil = \App\Models\PerfilProfesional::firstOrCreate(['user_id' => auth()->id()]);
    $fotos = $perfil?->fotos ?? collect();
    $negocio = $perfil?->negocio;
    $categorias = \App\Helpers\CategoriaHelper::arbol();

    return view('profesional.info', compact('servicios', 'fotos', 'negocio', 'categorias', 'perfil'));
})->middleware('auth')->name('profesional.info');

// ⚠️ Rutas específicas ANTES de la dinámica {id}
Route::get('/profesional/onboarding', function () {
    return view('profesional.onboarding');
})->middleware('auth')->name('profesional.onboarding');

Route::post('/profesional/vacaciones', function () {
    $perfil = \App\Models\PerfilProfesional::firstOrCreate(
        ['user_id' => auth()->id()]
    );
    $perfil->update(['en_vacaciones' => ! $perfil->en_vacaciones]);
    $mensaje = $perfil->en_vacaciones ? 'Modo vacaciones activado.' : 'Modo vacaciones desactivado.';

    return back()->with('success', $mensaje);
})->middleware('auth')->name('profesional.vacaciones');

Route::post('/profesional/cedula', [PerfilProfesionalController::class, 'guardarCedula'])
    ->middleware('auth')
    ->name('profesional.cedula');

Route::get('/profesional/dashboard', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }

    return view('profesional.dashboard');
})->middleware('auth')->name('profesional.dashboard');

Route::get('/profesional/perfil', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }

    return view('profesional.perfil');
})->middleware('auth')->name('profesional.perfil');

Route::get('/profesional/servicios', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }

    return view('profesional.servicios');
})->middleware('auth')->name('profesional.servicios.index');

Route::get('/profesional/resenas', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }

    return view('profesional.resenas');
})->middleware('auth')->name('profesional.resenas');

Route::get('/profesional/negocio', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return redirect()->route('inicio');
    }

    return view('profesional.negocio');
})->middleware('auth')->name('profesional.negocio');

Route::get('/profesional/analisis-resenas', function () {
    if (! auth()->check() || auth()->user()->role_id !== 2) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $resenas = auth()->user()->resenas()->with('cliente')->latest()->take(20)->get();

    if ($resenas->count() < 2) {
        return response()->json(['resumen' => null, 'mensaje' => 'Necesitas al menos 2 reseñas para generar el análisis.']);
    }

    $texto = $resenas->map(fn ($r) => "Cliente | Calificación: {$r->calificacion}/5 | Comentario: {$r->comentario}"
    )->join("\n");

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'x-api-key' => config('services.anthropic.key'),
        'anthropic-version' => '2023-06-01',
        'content-type' => 'application/json',
    ])->post('https://api.anthropic.com/v1/messages', [
        'model' => 'claude-haiku-4-5-20251001',
        'max_tokens' => 300,
        'messages' => [[
            'role' => 'user',
            'content' => "Eres un asistente que analiza reseñas de profesionales de servicios. Analiza estas reseñas y genera un resumen breve en español de máximo 3 frases: primero menciona los puntos fuertes que destacan los clientes, luego una sugerencia de mejora si aplica. Sé directo y positivo. Reseñas:\n\n{$texto}",
        ]],
    ]);

    $resumen = $response->json('content.0.text') ?? 'No se pudo generar el análisis.';

    return response()->json(['resumen' => $resumen]);
})->middleware('auth')->name('profesional.analisis.resenas');

// Ruta dinámica AL FINAL — debe ir siempre de última
Route::get('/profesional/{id}', [PerfilPublicoController::class, 'show'])
    ->name('profesional.publico');

Route::middleware('auth')->group(function () {

    // Horario profesional
    Route::post('/profesional/horario', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'dias_laborables' => 'required|array|min:1',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'dias_bloqueados' => 'nullable|array',
        ]);

        $perfil = \App\Models\PerfilProfesional::firstOrCreate(['user_id' => auth()->id()]);

        $perfil->update([
            'dias_laborables' => $request->dias_laborables,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'dias_bloqueados' => $request->dias_bloqueados ?? [],
        ]);

        return back()->with('success', 'Horario actualizado correctamente.');
    })->name('profesional.horario.guardar');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('inicio');
    })->name('logout');

    // Dashboard cliente
    Route::get('/cliente', function () {
        if (auth()->user()->role_id !== 1) {
            return redirect()->route('inicio');
        }

        return view('cliente.dashboard');
    })->name('cliente.dashboard');

    // Servicios
    Route::post('/profesional/servicios', [ProfesionalServicioController::class, 'store'])
        ->name('profesional.servicios.store');
    Route::delete('/profesional/servicios/{id}', [ServicioController::class, 'destroy'])
        ->name('profesional.servicios.destroy');
    Route::put('/profesional/servicios/{id}', [ServicioController::class, 'update'])
        ->name('profesional.servicios.update');

    // Perfil profesional
    Route::post('/perfil-profesional', [PerfilProfesionalController::class, 'guardar'])
        ->name('perfil.guardar');

    // Respuestas de reseñas
    Route::post('/resenas/{resenaId}/responder', [RespuestaResenaController::class, 'store'])
        ->name('resenas.responder');

    // Fotos
    Route::post('/perfil/fotos', [FotoPerfilController::class, 'store'])
        ->name('perfil.fotos.store');
    Route::delete('/perfil/fotos/{id}', [FotoPerfilController::class, 'destroy'])
        ->name('perfil.fotos.destroy');

    // Perfil usuario
    Route::get('/mi-perfil', [PerfilUsuarioController::class, 'index'])
        ->name('perfil.index');
    Route::post('/mi-perfil/actualizar', [PerfilUsuarioController::class, 'actualizar'])
        ->name('perfil.actualizar');
    Route::post('/mi-perfil/password', [PerfilUsuarioController::class, 'cambiarPassword'])
        ->name('perfil.password');

    // Negocio
    Route::post('/negocio/guardar', [NegocioController::class, 'guardar'])
        ->name('negocio.guardar');

    // Solicitudes
    Route::post('/solicitudes', [SolicitudController::class, 'store'])
        ->name('solicitudes.store');
    Route::post('/solicitudes/{id}/aceptar', [SolicitudController::class, 'aceptar'])
        ->name('solicitudes.aceptar');
    Route::post('/solicitudes/{id}/completar', [SolicitudController::class, 'completar'])
        ->name('solicitudes.completar');
    Route::post('/solicitudes/{id}/cancelar', [SolicitudController::class, 'cancelar'])
        ->name('solicitudes.cancelar');
    Route::get('/cliente/solicitudes', [SolicitudController::class, 'indexCliente'])
        ->name('cliente.solicitudes');

    // Reseñas
    Route::post('/profesional/{profesionalId}/resena', [ResenaController::class, 'store'])
        ->name('resenas.store');

    // Reservas
    Route::get('/reservar/{profesionalId}', [ReservaController::class, 'create'])->name('reservas.create');
    Route::get('/reservar/{profesionalId}/slots', [ReservaController::class, 'slots'])->name('reservas.slots');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::get('/reservas/{id}/pago', [ReservaController::class, 'pago'])->name('reservas.pago');
    Route::post('/reservas/{id}/pago', [ReservaController::class, 'procesarPago'])->name('reservas.pago.procesar');
    Route::get('/reservas/{id}/confirmacion', [ReservaController::class, 'confirmacion'])->name('reservas.confirmacion');
    Route::post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    Route::post('/reservas/{id}/completar', [ReservaController::class, 'completar'])->name('reservas.completar');
    Route::post('/reservas/{id}/confirmar', [ReservaController::class, 'confirmarCliente'])->name('reservas.confirmar');
    Route::post('/reservas/{id}/disputar', [ReservaController::class, 'disputarCliente'])->name('reservas.disputar');
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis');
    Route::get('/mis-reservas-pro', [ReservaController::class, 'reservasProfesional'])->name('profesional.reservas');
    Route::post('/reservas/{id}/aceptar', [ReservaController::class, 'aceptar'])->name('reservas.aceptar');
    // Exportar historial financiero
    Route::get('/profesional/historial-financiero/exportar', function () {
        $filename = 'historial-kliksy-'.now()->format('Y-m-d').'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\HistorialFinancieroExport, $filename);
    })->name('profesional.historial.exportar');
});

// --------------------
// RUTAS ADMIN
// --------------------
Route::middleware(['auth', 'es.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    Route::delete('/usuarios/{id}', [AdminController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::get('/servicios', [AdminController::class, 'servicios'])->name('servicios');
    Route::delete('/servicios/{id}', [AdminController::class, 'eliminarServicio'])->name('servicios.eliminar');
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
    Route::delete('/reportes/{resenaId}/resena', [AdminController::class, 'eliminarResena'])->name('reportes.eliminar');
    Route::patch('/reportes/{id}/ignorar', [AdminController::class, 'ignorarReporte'])->name('reportes.ignorar');
    Route::post('/perfil', [AdminController::class, 'actualizarPerfil'])->name('perfil');
    Route::get('/buscar', [AdminController::class, 'buscar'])->name('buscar');
    Route::get('/admins/crear', [AdminController::class, 'crearAdmin'])->name('admins.crear');
    Route::post('/admins/crear', [AdminController::class, 'guardarAdmin'])->name('admins.guardar');
    Route::get('/usuarios/{id}/detalle', [AdminController::class, 'verUsuario'])->name('usuarios.detalle');
    Route::delete('/reportes/servicio/{servicioId}', [AdminController::class, 'eliminarServicioReporte'])
        ->name('reportes.eliminarServicio');

    Route::patch('/reportes/servicio/{id}/ignorar', [AdminController::class, 'ignorarReporteServicio'])
        ->name('reportes.ignorarServicio');

    Route::post('/disputas/{id}/profesional', [AdminController::class, 'resolverDisputaProfesional'])->name('disputas.profesional');
    Route::post('/disputas/{id}/cliente', [AdminController::class, 'resolverDisputaCliente'])->name('disputas.cliente');
    Route::patch('/usuarios/{id}/deshabilitar', [AdminController::class, 'deshabilitarUsuario'])->name('usuarios.deshabilitar');
});
