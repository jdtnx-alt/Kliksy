<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->boolean('en_vacaciones')->default(false)->after('categorias');
        });
    }

    public function down(): void
    {
        Schema::table('perfiles_profesionales', function (Blueprint $table) {
            $table->dropColumn('en_vacaciones');
        });
    }
};
