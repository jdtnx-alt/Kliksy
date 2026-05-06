<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoPerfil extends Model
{
    protected $table = 'fotos_perfil';

    protected $fillable = ['perfil_profesional_id', 'ruta'];

    public function perfil()
    {
        return $this->belongsTo(PerfilProfesional::class, 'perfil_profesional_id');
    }
}
