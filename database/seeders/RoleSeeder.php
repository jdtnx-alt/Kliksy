<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'cliente',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'profesional', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'admin',       'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
