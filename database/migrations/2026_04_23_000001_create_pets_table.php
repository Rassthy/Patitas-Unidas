<?php

// Estas tres líneas importan las herramientas que necesita Laravel para crear tablas
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Una migración es como un "plano de obra" que le dice a la base de datos cómo construir la tabla
return new class extends Migration
{
    // Este método se ejecuta cuando corres "php artisan migrate" — construye la tabla
    public function up(): void
    {
        // Crea la tabla 'pets' con todas sus columnas
        Schema::create('pets', function (Blueprint $table) {
            $table->id();                                                  // Columna 'id' autoincremental (1, 2, 3...)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Clave foránea al dueño; si el usuario se borra, sus mascotas también
            $table->string('nombre', 100);                                 // Nombre de la mascota, máximo 100 caracteres
            $table->string('especie', 50)->nullable();                     // Especie (Perro, Gato…) — puede estar vacío
            $table->string('raza', 50)->nullable();                        // Raza — también opcional
            $table->unsignedInteger('edad')->nullable();                   // Edad en años, sin negativos — opcional
            $table->text('descripcion')->nullable();                       // Descripción larga — opcional
            $table->string('foto', 255)->nullable();                       // Ruta a la foto guardada — opcional
            $table->timestamps();                                          // Crea 'created_at' y 'updated_at' automáticamente
        });
    }

    // Este método se ejecuta si "deshaces" la migración — simplemente borra la tabla
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
