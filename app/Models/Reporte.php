<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $fillable = ['resena_id', 'user_id', 'motivo', 'estado'];

    public function resena()
    {
        return $this->belongsTo(Resena::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
