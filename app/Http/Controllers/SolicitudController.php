<?php

namespace App\Http\Controllers;

use App\Mail\SolicitudRecibida;
use App\Models\Servicio;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SolicitudController extends Controller
{
    // Cliente crea una solicitud
    public function store(Request $request)
    {
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);

        // Verificar que no tenga una solicitud pendiente o aceptada para el mismo servicio
        $existe = Solicitud::where('cliente_id', Auth::id())
            ->where('servicio_id', $servicio->id)
            ->whereIn('estado', ['pendiente', 'aceptada'])
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya tienes una solicitud activa para este servicio');
        }

        $solicitud = Solicitud::create([
            'servicio_id' => $servicio->id,
            'cliente_id' => Auth::id(),
            'profesional_id' => $servicio->user_id,
            'fecha' => now(),
            'estado' => 'pendiente',
        ]);

        // Cargar relaciones para el correo
        $solicitud->load(['servicio', 'cliente']);

        // Enviar correo al profesional
        Mail::to($servicio->user->email)->send(new SolicitudRecibida($solicitud));

        return back()->with('success', 'Solicitud enviada correctamente');
    }

    public function aceptar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update(['estado' => 'aceptada']);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Solicitud aceptada');
    }

    public function completar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update(['estado' => 'completada']);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Servicio completado');
    }

    public function cancelar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update(['estado' => 'cancelada']);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Solicitud cancelada');
    }

    // Solicitudes del profesional
    public function indexProfesional()
    {
        $solicitudes = Solicitud::with(['servicio', 'cliente'])
            ->where('profesional_id', Auth::id())
            ->orderByRaw("FIELD(estado, 'pendiente', 'aceptada', 'completada', 'cancelada')")
            ->get();

        return view('profesional.solicitudes', compact('solicitudes'));
    }

    // Solicitudes del cliente
    public function indexCliente()
    {
        $solicitudes = Solicitud::with(['servicio', 'profesional'])
            ->where('cliente_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('cliente.solicitudes', compact('solicitudes'));
    }
}
