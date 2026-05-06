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
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->integer('duracion_promedio')->nullable()->default(60)->after('en_vacaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->dropColumn('duracion_promedio');
        });
    }
};
