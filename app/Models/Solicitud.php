<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $fillable = [
        'servicio_id',
        'cliente_id',
        'profesional_id',
        'fecha',
        'estado',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
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
