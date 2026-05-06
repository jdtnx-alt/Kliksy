<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    protected $table = 'negocios';

    protected $fillable = [
        'perfil_profesional_id',
        'nombre',
        'descripcion',
        'direccion',
        'telefono',
        'categoria',
    ];

    public function perfil()
    {
        return $this->belongsTo(PerfilProfesional::class, 'perfil_profesional_id');
    }
}
