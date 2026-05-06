<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputaReserva extends Model
{
    protected $table = 'disputas_reserva';

    protected $fillable = [
        'reserva_id',
        'cliente_id',
        'profesional_id',
        'motivo',
        'estado',
        'resolucion_admin',
        'resuelto_at',
    ];

    protected $casts = [
        'resuelto_at' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }
}
