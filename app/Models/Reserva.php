<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'cliente_id',
        'profesional_id',
        'servicio_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'nota_cliente',
        'estado_pago',
        'monto',
        'liberacion_automatica_at',
        'liberado_at',
        'confirmacion_cliente',
        'liberacion_cliente_at',
        'confirmado_at',
        'disputado_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'liberacion_automatica_at' => 'datetime',
        'liberado_at' => 'datetime',
        'liberacion_cliente_at' => 'datetime',
        'confirmado_at' => 'datetime',
        'disputado_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function disputa()
    {
        return $this->hasOne(DisputaReserva::class);
    }
}
