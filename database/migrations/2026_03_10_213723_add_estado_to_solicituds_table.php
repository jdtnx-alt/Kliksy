<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicituds', function (Blueprint $table) {
            $table->foreignId('profesional_id')
                ->after('cliente_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'aceptada', 'completada', 'cancelada'])
                ->default('pendiente')
                ->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('solicituds', function (Blueprint $table) {
            $table->dropForeign(['profesional_id']);
            $table->dropColumn(['profesional_id', 'estado']);
        });
    }
};
