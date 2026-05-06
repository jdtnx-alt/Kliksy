<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservaController extends Controller
{
    public function create(Request $request, $profesionalId)
    {
        $profesional = User::with(['servicios', 'perfilProfesional'])
            ->where('role_id', 2)
            ->findOrFail($profesionalId);

        $servicioId = $request->get('servicio_id');
        $servicio = $servicioId
            ? Servicio::where('user_id', $profesionalId)->findOrFail($servicioId)
            : $profesional->servicios->first();

        return view('reservas.create', compact('profesional', 'servicio'));
    }

    public function slots(Request $request, $profesionalId)
    {
        $fecha = $request->get('fecha');
        $servicioId = $request->get('servicio_id');

        if (! $fecha || ! $servicioId) {
            return response()->json([]);
        }

        $servicio = Servicio::findOrFail($servicioId);

        // Obtener horario del profesional
        $perfil = \App\Models\PerfilProfesional::where('user_id', $profesionalId)->first();

        // Usar horario personalizado o valores por defecto
        $horaInicio = $perfil?->hora_inicio ?? '08:00';
        $horaFin = $perfil?->hora_fin ?? '18:00';

        $inicio = Carbon::parse($fecha.' '.$horaInicio, 'America/Bogota');
        $fin = Carbon::parse($fecha.' '.$horaFin, 'America/Bogota');

        $duracionPromedio = $perfil?->duracion_promedio;
        
        // Si es 0 (Todo el día), la duración será exactamente la diferencia entre la apertura y cierre
        if ($duracionPromedio === 0) {
            $duracion = clone $inicio;
            $duracion = $duracion->diffInMinutes($fin);
            if ($duracion <= 0) $duracion = 1440; // Fallback
        } elseif ($duracionPromedio && $duracionPromedio > 0) {
            $duracion = $duracionPromedio;
        } else {
            $duracion = $servicio->duracion ?? 60;
        }

        // Verificar si el día está bloqueado
        $diasBloqueados = $perfil?->dias_bloqueados ?? [];
        if (in_array($fecha, $diasBloqueados)) {
            return response()->json([]);
        }

        // Verificar si el día de la semana está habilitado
        $diasLaborables = $perfil?->dias_laborables ?? ['lun', 'mar', 'mie', 'jue', 'vie'];
        $mapaDias = [
            1 => 'lun', 2 => 'mar', 3 => 'mie',
            4 => 'jue', 5 => 'vie', 6 => 'sab', 7 => 'dom',
        ];
        $diaSemana = Carbon::parse($fecha)->dayOfWeekIso;
        if (! in_array($mapaDias[$diaSemana], $diasLaborables)) {
            return response()->json([]);
        }

        $reservasOcupadas = Reserva::where('profesional_id', $profesionalId)
            ->where('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada'])
            ->get();

        $slots = [];
        $cursor = $inicio->copy();

        while ($cursor->copy()->addMinutes($duracion)->lte($fin)) {
            $slotInicio = $cursor->copy();
            $slotFin = $cursor->copy()->addMinutes($duracion);

            $ocupado = $reservasOcupadas->contains(function ($r) use ($slotInicio, $slotFin, $fecha) {
                $rI = Carbon::parse($fecha.' '.$r->hora_inicio, 'America/Bogota');
                $rF = Carbon::parse($fecha.' '.$r->hora_fin, 'America/Bogota');

                return $slotInicio->lt($rF) && $slotFin->gt($rI);
            });

            $slots[] = [
                'hora_inicio' => $slotInicio->format('H:i'),
                'hora_fin' => $slotFin->format('H:i'),
                'disponible' => ! $ocupado && $slotInicio->isFuture(),
                'label' => $slotInicio->format('g:i A').' – '.$slotFin->format('g:i A'),
            ];

            $cursor->addMinutes($duracion);
        }

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $request->validate([
            'profesional_id' => 'required|exists:users,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'nota_cliente' => 'nullable|string|max:300',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        
        $perfil = \App\Models\PerfilProfesional::where('user_id', $request->profesional_id)->first();
        $duracionPromedio = $perfil?->duracion_promedio;
        
        $horaInicio = Carbon::parse($request->fecha.' '.$request->hora_inicio, 'America/Bogota');
        
        if ($duracionPromedio === 0) {
            $horaApertura = Carbon::parse($request->fecha.' '.($perfil?->hora_inicio ?? '08:00'), 'America/Bogota');
            $horaCierre = Carbon::parse($request->fecha.' '.($perfil?->hora_fin ?? '18:00'), 'America/Bogota');
            $duracion = $horaApertura->diffInMinutes($horaCierre);
            if ($duracion <= 0) $duracion = 1440;
        } elseif ($duracionPromedio !== null && $duracionPromedio > 0) {
            $duracion = $duracionPromedio;
        } else {
            $duracion = $servicio->duracion ?? 60;
        }

        $horaFin = $horaInicio->copy()->addMinutes($duracion);

        if ($horaInicio->isPast()) {
            return back()->with('error', 'No puedes reservar en un horario que ya pasó.');
        }

        $choque = Reserva::where('profesional_id', $request->profesional_id)
            ->where('fecha', $request->fecha)
            ->whereNotIn('estado', ['cancelada'])
            ->where('hora_inicio', '<', $horaFin->format('H:i:s'))
            ->where('hora_fin', '>', $horaInicio->format('H:i:s'))
            ->exists();

        if ($choque) {
            return back()->with('error', 'Este horario ya no está disponible.');
        }

        $reserva = Reserva::create([
            'cliente_id' => auth()->id(),
            'profesional_id' => $request->profesional_id,
            'servicio_id' => $request->servicio_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $horaInicio->format('H:i:s'),
            'hora_fin' => $horaFin->format('H:i:s'),
            'estado' => 'pendiente',
            'nota_cliente' => $request->nota_cliente,
            'estado_pago' => 'pendiente',
            'monto' => $servicio->precio,
        ]);

        return redirect()->route('reservas.pago', $reserva->id);
    }

    public function pago($id)
    {
        $reserva = Reserva::with(['servicio', 'profesional.perfilProfesional'])
            ->where('cliente_id', auth()->id())
            ->findOrFail($id);

        return view('reservas.pago', compact('reserva'));
    }

    public function procesarPago(Request $request, $id)
    {
        $request->merge([
            'numero_tarjeta' => preg_replace('/\s+/', '', $request->numero_tarjeta),
        ]);

        $request->validate([
            'numero_tarjeta' => 'required|digits:16',
            'nombre_tarjeta' => 'required|string',
            'vencimiento' => 'required',
            'cvv' => 'required|digits:3',
        ]);

        $reserva = Reserva::with(['profesional', 'servicio', 'cliente'])
            ->where('cliente_id', auth()->id())
            ->findOrFail($id);

        $reserva->update([
            'estado_pago' => 'retenido',
            'estado' => 'pendiente',
            'liberacion_automatica_at' => now()->addHours(48),
        ]);

        // Enviar correo al profesional
        $profesional = $reserva->profesional;
        $cliente = $reserva->cliente;
        $servicio = $reserva->servicio;

        Mail::send('emails.nueva-reserva', [
            'profesional' => $profesional,
            'cliente' => $cliente,
            'reserva' => $reserva,
            'servicio' => $servicio,
        ], function ($m) use ($profesional) {
            $m->to($profesional->email)
                ->subject('Nueva reserva recibida — Kliksy');
        });

        return redirect()->route('reservas.confirmacion', $reserva->id);
    }

    public function confirmacion($id)
    {
        $reserva = Reserva::with(['servicio', 'profesional.perfilProfesional'])
            ->where('cliente_id', auth()->id())
            ->findOrFail($id);

        return view('reservas.confirmacion', compact('reserva'));
    }

    public function aceptar($id)
    {
        $reserva = Reserva::where('profesional_id', auth()->id())
            ->where('estado', 'pendiente')
            ->findOrFail($id);

        $reserva->update(['estado' => 'confirmada']);

        return back()->with('success', 'Reserva confirmada.');
    }

    public function cancelar($id)
    {
        $reserva = Reserva::where(function ($q) {
            $q->where('cliente_id', auth()->id())
                ->orWhere('profesional_id', auth()->id());
        })->findOrFail($id);

        $reembolso = in_array($reserva->estado_pago, ['retenido']);

        $reserva->update([
            'estado' => 'cancelada',
            'estado_pago' => $reembolso ? 'reembolsado' : $reserva->estado_pago,
        ]);

        return back()->with('success', $reembolso
            ? 'Reserva cancelada. El pago será reembolsado.'
            : 'Reserva cancelada.');
    }

    public function completar($id)
    {
        $reserva = Reserva::with(['cliente', 'servicio', 'profesional'])
            ->where('profesional_id', auth()->id())
            ->where('estado', 'confirmada')
            ->findOrFail($id);

        $reserva->update([
            'estado' => 'completada',
            'confirmacion_cliente' => 'pendiente',
            'liberacion_cliente_at' => now()->addDays(3),
        ]);

        // Correo al cliente
        Mail::send('emails.reserva-completada', [
            'cliente' => $reserva->cliente,
            'profesional' => $reserva->profesional,
            'reserva' => $reserva,
            'servicio' => $reserva->servicio,
        ], function ($m) use ($reserva) {
            $m->to($reserva->cliente->email)
                ->subject('Tu servicio fue completado — Kliksy');
        });

        return back()->with('success', 'Servicio marcado como completado. El cliente tiene 3 días para confirmar, tras lo cual se liberará el pago.');
    }

    public function confirmarCliente($id)
    {
        $reserva = Reserva::where('cliente_id', auth()->id())
            ->whereIn('estado', ['pendiente', 'confirmada', 'completada'])
            ->where(function($q) {
                $q->whereNull('confirmacion_cliente')
                  ->orWhere('confirmacion_cliente', 'pendiente');
            })
            ->findOrFail($id);

        $reserva->update([
            'estado' => 'completada',
            'confirmacion_cliente' => 'confirmado',
            'confirmado_at' => now(),
            'estado_pago' => 'liberado',
            'liberado_at' => now(),
        ]);

        return back()->with('success', 'Has confirmado que el servicio fue completado. Se ha liberado el pago al profesional.');
    }

    public function disputarCliente(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:1000',
        ]);

        $reserva = Reserva::where('cliente_id', auth()->id())
            ->whereIn('estado', ['pendiente', 'confirmada', 'completada'])
            ->where(function($q) {
                $q->whereNull('confirmacion_cliente')
                  ->orWhere('confirmacion_cliente', 'pendiente');
            })
            ->findOrFail($id);

        $reserva->update([
            'estado' => 'completada',
            'confirmacion_cliente' => 'disputado',
            'disputado_at' => now(),
        ]);

        \App\Models\DisputaReserva::create([
            'reserva_id' => $reserva->id,
            'cliente_id' => $reserva->cliente_id,
            'profesional_id' => $reserva->profesional_id,
            'motivo' => $request->motivo,
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Has reportado el servicio como no completado. El administrador revisará el caso y el pago se mantendrá retenido.');
    }

    // Vista de reservas del profesional
    public function reservasProfesional(Request $request)
    {
        $query = Reserva::with(['servicio', 'cliente'])
            ->where('profesional_id', auth()->id());

        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $reservas = $query->orderByDesc('fecha')->orderByDesc('hora_inicio')->get();

        // Agrupar por fecha
        $reservasPorFecha = $reservas->groupBy(fn ($r) => $r->fecha->format('Y-m-d'));

        return view('reservas.profesional', compact('reservasPorFecha', 'reservas'));
    }

    public function misReservas()
    {
        $reservas = Reserva::with(['servicio', 'profesional.perfilProfesional'])
            ->where('cliente_id', auth()->id())
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('reservas.mis-reservas', compact('reservas'));
    }
}
