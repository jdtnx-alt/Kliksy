<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: cambiar a string para poder actualizar libremente
        \DB::statement("ALTER TABLE reservas MODIFY estado_pago VARCHAR(20) NOT NULL DEFAULT 'pendiente'");

        // Paso 2: actualizar valor viejo
        \DB::statement("UPDATE reservas SET estado_pago = 'retenido' WHERE estado_pago = 'simulado'");

        // Paso 3: cambiar al nuevo enum
        \DB::statement("ALTER TABLE reservas MODIFY estado_pago ENUM('pendiente', 'retenido', 'liberado', 'reembolsado') NOT NULL DEFAULT 'pendiente'");

        // Paso 4: agregar columnas nuevas
        Schema::table('reservas', function (Blueprint $table) {
            $table->timestamp('liberacion_automatica_at')->nullable()->after('monto');
            $table->timestamp('liberado_at')->nullable()->after('liberacion_automatica_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['liberacion_automatica_at', 'liberado_at']);
        });
    }
};
