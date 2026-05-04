<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Insertamos las categorías base
        DB::table('forum_categories')->insert([
            ['name' => 'Adoptar mascota', 'description' => 'Publicaciones de adopcion', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mascota perdida o robada', 'description' => 'Publicaciones de mascotas perdidas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Apoyar animales', 'description' => 'Publicaciones de apoyo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
