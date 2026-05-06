<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Desactivar restricciones de FK temporalmente ──────────────
        Schema::disableForeignKeyConstraints();

        // ── 2. Limpiar tablas en orden inverso de dependencias ────────────
        DB::table('negocios')->truncate();
        DB::table('fotos_perfil')->truncate();
        DB::table('respuestas_resena')->truncate();
        DB::table('resenas')->truncate();
        DB::table('reportes')->truncate();
        DB::table('solicituds')->truncate();
        DB::table('servicios')->truncate();
        DB::table('perfiles_profesionales')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();

        // ── 3. Reactivar FK ───────────────────────────────────────────────
        Schema::enableForeignKeyConstraints();

        // ── 4. Sembrar en orden de dependencias ───────────────────────────
        $this->call([
            RoleSeeder::class,       // roles primero
            ClienteSeeder::class,    // clientes (role_id = 1)
            ProfesionalSeeder::class, // profesionales + perfiles + servicios + negocios
        ]);
    }
}
