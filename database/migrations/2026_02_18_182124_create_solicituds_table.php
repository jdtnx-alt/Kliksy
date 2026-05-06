<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicituds', function (Blueprint $table) {

            $table->id();

            $table->foreignId('servicio_id')->constrained()->onDelete('cascade');

            $table->foreignId('cliente_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->dateTime('fecha');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicituds');
    }
};
