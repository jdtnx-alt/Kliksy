<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            // 'categoria' ya existe como el PADRE (ej: 'belleza')
            // 'subcategoria' es la nueva (ej: 'manicura')
            $table->string('subcategoria')->nullable()->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('subcategoria');
        });
    }
};
