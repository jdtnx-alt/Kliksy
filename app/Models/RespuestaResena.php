<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaResena extends Model
{
    protected $table = 'respuestas_resena';

    protected $fillable = [
        'resena_id',
        'user_id',
        'contenido',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resena()
    {
        return $this->belongsTo(Resena::class);
    }
}
