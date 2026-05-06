<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'telefono', 'password', 'google_id', 'role_id', 'onboarding_completado', 'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Eliminar solicitudes como cliente
            \App\Models\Solicitud::where('cliente_id', $user->id)->delete();
            // Eliminar solicitudes como profesional
            \App\Models\Solicitud::where('profesional_id', $user->id)->delete();
            // Eliminar reseñas hechas por el cliente
            \App\Models\Resena::where('cliente_id', $user->id)->delete();
            // Eliminar reportes hechos por el usuario
            \App\Models\Reporte::where('user_id', $user->id)->delete();
            // El perfil, servicios, fotos y negocio ya tienen cascade en la migración
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'cliente_id');
    }

    public function perfilProfesional()
    {
        return $this->hasOne(PerfilProfesional::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'profesional_id');
    }

    public function promedioCalificacion()
    {
        return $this->resenas()->avg('calificacion') ?? 0;
    }
}
