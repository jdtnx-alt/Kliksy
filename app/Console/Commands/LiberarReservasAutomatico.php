<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use Illuminate\Support\Facades\Log;

class LiberarReservasAutomatico extends Command
{
    protected $signature = 'reservas:liberar-automatico';

    protected $description = 'Libera el pago a los profesionales luego de 3 días sin respuesta del cliente.';

    public function handle()
    {
        $reservas = Reserva::where('estado', 'completada')
            ->where('confirmacion_cliente', 'pendiente')
            ->where('liberacion_cliente_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($reservas as $reserva) {
            $reserva->update([
                'confirmacion_cliente' => 'confirmado',
                'confirmado_at' => now(),
                'estado_pago' => 'liberado',
                'liberado_at' => now(),
            ]);
            $count++;
        }

        if ($count > 0) {
            Log::info("Se liberaron $count reservas automaticamente.");
            $this->info("Se liberaron $count reservas automaticamente.");
        } else {
            $this->info("No hay reservas pendientes de liberacion automatica.");
        }
    }
}
