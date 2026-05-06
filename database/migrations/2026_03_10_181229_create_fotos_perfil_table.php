<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos_perfil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_profesional_id')
                ->constrained('perfiles_profesionales')
                ->onDelete('cascade');
            $table->string('ruta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_perfil');
    }
};
