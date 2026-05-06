<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';

    protected $fillable = [
        'servicio_id',
        'cliente_id',
        'profesional_id',
        'calificacion',
        'comentario',
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

    public function respuestas()
    {
        return $this->hasMany(RespuestaResena::class, 'resena_id');
    }
}
