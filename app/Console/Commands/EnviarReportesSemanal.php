<?php

namespace App\Console\Commands;

use App\Mail\ReporteSemanalMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarReportesSemanal extends Command
{
    protected $signature = 'kliksy:reporte-semanal';

    protected $description = 'Envía el reporte semanal a todos los profesionales activos';

    public function handle()
    {
        $profesionales = User::where('role_id', 2)
            ->whereHas('perfilProfesional', function ($q) {
                $q->whereNotNull('descripcion')
                    ->whereNotNull('whatsapp')
                    ->where('en_vacaciones', false);
            })
            ->get();

        $this->info("Enviando reportes a {$profesionales->count()} profesionales...");

        foreach ($profesionales as $profesional) {
            try {
                Mail::to($profesional->email)->send(new ReporteSemanalMail($profesional));
                $this->info("✓ Reporte enviado a {$profesional->email}");
            } catch (\Exception $e) {
                $this->error("✗ Error enviando a {$profesional->email}: {$e->getMessage()}");
            }
        }

        $this->info('Reportes enviados correctamente.');
    }
}
