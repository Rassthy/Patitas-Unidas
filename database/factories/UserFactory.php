<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $dniCounter = 10000000;
        static $phoneCounter = 600000000;

        return [
            'username' => $this->faker->unique()->userName(),
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'dni_nie' => (string)($dniCounter++) . 'A',
            'telefono' => (string)($phoneCounter++),
            'password_hash' => static::$password ??= Hash::make('TestPass123'),
            'tipo' => 'usuario',
            'activo' => true,
            'email_verificado' => false,
            'telefono_verificado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verificado' => true,
            'telefono_verificado' => true,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verificado' => false,
            'telefono_verificado' => false,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a shelter.
     */
    public function shelter(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'protectora',
        ]);
    }

    /**
     * Indicate that the user is an organization.
     */
    public function organization(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'organizacion',
        ]);
    }
}
