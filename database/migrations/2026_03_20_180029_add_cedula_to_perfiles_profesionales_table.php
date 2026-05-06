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
            $table->string('cedula_frontal')->nullable();
            $table->string('cedula_trasera')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->dropColumn(['cedula_frontal', 'cedula_trasera']);
        });
    }
};
