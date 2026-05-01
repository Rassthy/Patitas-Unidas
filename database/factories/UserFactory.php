<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'username'             => $this->faker->unique()->userName(),
            'dni_nie'              => strtoupper($this->faker->unique()->bothify('########?')),
            'telefono'             => $this->faker->unique()->numerify('6########'),
            'email'                => $this->faker->unique()->safeEmail(),
            'password_hash'        => Hash::make('password'),
            'nombre'               => $this->faker->firstName(),
            'apellidos'            => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'tipo'                 => 'usuario',
            'descripcion'          => $this->faker->optional()->sentence(),
            'fecha_nacimiento'     => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'provincia'            => $this->faker->randomElement(['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao']),
            'ciudad'               => $this->faker->city(),
            'email_verificado'     => false,
            'telefono_verificado'  => false,
            'activo'               => true,
            'motivo_baja'          => null,
        ];
    }
}