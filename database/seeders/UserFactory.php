<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $nombresM = ['Andrés', 'Carlos', 'Diego', 'Felipe', 'Jorge', 'Juan', 'Luis', 'Mauricio', 'Miguel', 'Sebastián', 'Camilo', 'Alejandro', 'Ricardo', 'Oscar', 'David'];
        $nombresF = ['Valentina', 'Daniela', 'Alejandra', 'Catalina', 'Natalia', 'Laura', 'Juliana', 'Paola', 'Adriana', 'Mónica', 'Carolina', 'Sandra', 'Marcela', 'Diana', 'Ximena'];
        $apellidos = ['García', 'Martínez', 'López', 'Rodríguez', 'González', 'Hernández', 'Pérez', 'Ramírez', 'Torres', 'Flores', 'Vargas', 'Mora', 'Castro', 'Jiménez', 'Ruiz', 'Díaz', 'Reyes', 'Morales', 'Ortiz', 'Suárez'];

        $esFemenino = $this->faker->boolean();
        $nombre = $esFemenino
            ? $this->faker->randomElement($nombresF)
            : $this->faker->randomElement($nombresM);
        $apellido1 = $this->faker->randomElement($apellidos);
        $apellido2 = $this->faker->randomElement($apellidos);

        return [
            'name' => "$nombre $apellido1 $apellido2",
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Kliksy2026*'),
            'telefono' => '3'.$this->faker->numerify('## ### ####'),
            'role_id' => 1,
            'onboarding_completado' => true,
        ];
    }

    public function cliente(): static
    {
        return $this->state(['role_id' => 1]);
    }

    public function profesional(): static
    {
        return $this->state(['role_id' => 2]);
    }
}
