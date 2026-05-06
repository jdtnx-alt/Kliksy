<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputas_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('profesional_id')->constrained('users')->cascadeOnDelete();
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'resuelto_cliente', 'resuelto_profesional'])->default('pendiente');
            $table->text('resolucion_admin')->nullable();
            $table->timestamp('resuelto_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputas_reserva');
    }
};
