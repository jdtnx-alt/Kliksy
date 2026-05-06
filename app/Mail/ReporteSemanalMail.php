<?php

namespace App\Mail;

use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReporteSemanalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombreProfesional;

    public float $ingresosSemana;

    public float $ingresosSemanaAnterior;

    public int $reservasSemana;

    public float $promedioCalificacion;

    public int $nuevasResenas;

    public $reservasDetalle;

    public int $variacion;

    public function __construct(User $profesional)
    {
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();
        $inicioSemanaAnt = Carbon::now()->subWeek()->startOfWeek();
        $finSemanaAnt = Carbon::now()->subWeek()->endOfWeek();

        $this->nombreProfesional = explode(' ', $profesional->name)[0];

        $this->reservasDetalle = Reserva::with(['cliente', 'servicio'])
            ->where('profesional_id', $profesional->id)
            ->where('estado', 'completada')
            ->whereBetween('fecha', [$inicioSemana, $finSemana])
            ->get();

        $this->ingresosSemana = Reserva::where('profesional_id', $profesional->id)
            ->where('estado_pago', 'liberado')
            ->whereBetween('fecha', [$inicioSemana, $finSemana])
            ->sum('monto');

        $this->ingresosSemanaAnterior = Reserva::where('profesional_id', $profesional->id)
            ->where('estado_pago', 'liberado')
            ->whereBetween('fecha', [$inicioSemanaAnt, $finSemanaAnt])
            ->sum('monto');

        $this->reservasSemana = $this->reservasDetalle->count();

        $this->promedioCalificacion = $profesional->resenas()
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->avg('calificacion') ?? 0;

        $this->nuevasResenas = $profesional->resenas()
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->count();

        $this->variacion = $this->ingresosSemanaAnterior > 0
            ? round((($this->ingresosSemana - $this->ingresosSemanaAnterior) / $this->ingresosSemanaAnterior) * 100)
            : 0;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Tu reporte semanal — Kliksy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reporte-semanal',
            with: [
                'nombreProfesional' => $this->nombreProfesional,
                'ingresosSemana' => $this->ingresosSemana,
                'ingresosSemanaAnterior' => $this->ingresosSemanaAnterior,
                'reservasSemana' => $this->reservasSemana,
                'promedioCalificacion' => $this->promedioCalificacion,
                'nuevasResenas' => $this->nuevasResenas,
                'reservasDetalle' => $this->reservasDetalle,
                'variacion' => $this->variacion,
            ],
        );
    }
}
