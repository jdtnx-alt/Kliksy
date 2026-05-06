<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->enum('confirmacion_cliente', ['pendiente', 'confirmado', 'disputado'])->nullable()->after('estado_pago');
            $table->timestamp('liberacion_cliente_at')->nullable()->after('confirmacion_cliente');
            $table->timestamp('confirmado_at')->nullable()->after('liberacion_cliente_at');
            $table->timestamp('disputado_at')->nullable()->after('confirmado_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['confirmacion_cliente', 'liberacion_cliente_at', 'confirmado_at', 'disputado_at']);
        });
    }
};
