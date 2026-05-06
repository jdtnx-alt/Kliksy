<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles_profesionales', function (Blueprint $table) {
            $table->id();

            // Relación con usuario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Datos del perfil
            $table->text('descripcion')->nullable();
            $table->string('experiencia')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('whatsapp')->nullable();

            // Categorías
            $table->json('categorias')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles_profesionales');
    }
};
