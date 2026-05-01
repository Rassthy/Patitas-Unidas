<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ForumCategory::create([
            'name' => 'Adoptar mascota',
            'description' => 'Publicaciones sobre mascotas disponibles para adopción',
            'icon' => '🏠',
            'order' => 1,
            'active' => true,
        ]);

        \App\Models\ForumCategory::create([
            'name' => 'Mascota perdida',
            'description' => 'Ayuda para encontrar mascotas perdidas',
            'icon' => '🔍',
            'order' => 2,
            'active' => true,
        ]);

        \App\Models\ForumCategory::create([
            'name' => 'Apoyar animales',
            'description' => 'Publicaciones sobre apoyo y ayuda a animales',
            'icon' => '❤️',
            'order' => 3,
            'active' => true,
        ]);
    }
}
