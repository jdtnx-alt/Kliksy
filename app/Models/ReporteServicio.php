<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteServicio extends Model
{
    protected $table = 'reportes_servicios';

    protected $fillable = [
        'servicio_id',
        'profesional_id',
        'user_id',
        'motivo',
        'tipo',
        'estado',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
