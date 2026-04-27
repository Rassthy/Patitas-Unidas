<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Ejecuta los seeders de la base de datos.
     */
    public function run(): void
    {
        // Usuario de admin
        User::firstOrCreate(
            ['email' => 'admin@patitasunidas.es'],
            [
                'username' => 'admin',
                'nombre' => 'Administrador',
                'apellidos' => 'PatitasUnidas',
                'dni_nie' => '00000000A',
                'telefono' => '600000000',
                'password_hash' => Hash::make('Admin123'),
                'tipo' => 'admin',
                'activo' => true,
                'email_verificado' => true,
                'telefono_verificado' => true,
            ]
        );

        // Usuario de protectora
        User::firstOrCreate(
            ['email' => 'protectora@example.com'],
            [
                'username' => 'protectora_test',
                'nombre' => 'Protectora',
                'apellidos' => 'De Prueba',
                'dni_nie' => '11111111B',
                'telefono' => '611111111',
                'password_hash' => Hash::make('Protectora123'),
                'tipo' => 'protectora',
                'activo' => true,
                'email_verificado' => true,
                'telefono_verificado' => true,
            ]
        );

        // Usuario regular para pruebas
        User::firstOrCreate(
            ['email' => 'usuario@example.com'],
            [
                'username' => 'usuario_test',
                'nombre' => 'Usuario',
                'apellidos' => 'De Prueba',
                'dni_nie' => '22222222C',
                'telefono' => '622222222',
                'password_hash' => Hash::make('Usuario123'),
                'tipo' => 'usuario',
                'activo' => true,
                'email_verificado' => false,
                'telefono_verificado' => false,
            ]
        );

        // 10 usuarios aleatorios adicionales
        User::factory(10)->create();
    }
}
