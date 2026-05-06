<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->json('dias_laborables')->nullable()->after('en_vacaciones');
            $table->time('hora_inicio')->nullable()->after('dias_laborables');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->json('dias_bloqueados')->nullable()->after('hora_fin');
        });
    }

    public function down(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->dropColumn(['dias_laborables', 'hora_inicio', 'hora_fin', 'dias_bloqueados']);
        });
    }
};
