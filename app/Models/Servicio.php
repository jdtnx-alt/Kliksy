<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'precio',
        'categoria',
        'subcategoria',
        'duracion',
        'foto',
        'fotos',
        'user_id',
    ];

    protected $casts = [
        'fotos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
