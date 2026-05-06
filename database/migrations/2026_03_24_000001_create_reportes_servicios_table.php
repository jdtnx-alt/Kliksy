<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->onDelete('cascade');
            $table->foreignId('profesional_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('motivo');
            $table->enum('tipo', ['servicio', 'profesional'])->default('servicio');
            $table->enum('estado', ['pendiente', 'revisado', 'eliminado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_servicios');
    }
};
