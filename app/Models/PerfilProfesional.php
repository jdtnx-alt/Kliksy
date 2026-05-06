<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilProfesional extends Model
{
    protected $table = 'perfiles_profesionales';

    protected $fillable = [
        'user_id',
        'descripcion',
        'experiencia',
        'ubicacion',
        'whatsapp',
        'categorias',
        'en_vacaciones',
        'dias_laborables',
        'hora_inicio',
        'hora_fin',
        'dias_bloqueados',
        'duracion_promedio',
    ];

    protected $casts = [
        'categorias' => 'array',
        'dias_laborables' => 'array',
        'dias_bloqueados' => 'array',
    ];

    // ✅ Agrega esto
    public function fotos()
    {
        return $this->hasMany(FotoPerfil::class, 'perfil_profesional_id');
    }

    public function negocio()
    {
        return $this->hasOne(Negocio::class, 'perfil_profesional_id');
    }
}
