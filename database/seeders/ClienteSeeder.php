<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // 4 clientes ficticios
        $clientes = [
            [
                'name' => 'Sofía Ramírez Torres',
                'email' => 'sofia.ramirez.kliksy@example.com',
                'telefono' => '3112345678',
            ],
            [
                'name' => 'Mateo González Vargas',
                'email' => 'mateo.gonzalez.kliksy@example.com',
                'telefono' => '3223456789',
            ],
            [
                'name' => 'Valeria Morales Castro',
                'email' => 'valeria.morales.kliksy@example.com',
                'telefono' => '3134567890',
            ],
            [
                'name' => 'Sebastián Díaz Ruiz',
                'email' => 'sebastian.diaz.kliksy@example.com',
                'telefono' => '3045678901',
            ],
        ];

        foreach ($clientes as $c) {
            User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Kliksy2026*'),
                'telefono' => $c['telefono'],
                'role_id' => 1,
                'onboarding_completado' => true,
            ]);
        }

        // Correos reales como clientes
        $correosClientes = [
            ['name' => 'Yuleiny Lugo',    'email' => 'yuleinylugo71@gmail.com',  'telefono' => '3156789012'],
            ['name' => 'Yuleiny Vargas',  'email' => 'yuleiny798@gmail.com',     'telefono' => '3167890123'],
        ];

        foreach ($correosClientes as $c) {
            User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Kliksy2026*'),
                'telefono' => $c['telefono'],
                'role_id' => 1,
                'onboarding_completado' => true,
            ]);
        }
    }
}
