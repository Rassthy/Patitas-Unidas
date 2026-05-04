<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla para guardar el historial de vacunas de cada mascota
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_vaccines', function (Blueprint $table) {
            $table->id();                                                  // ID único de la vacuna
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete(); // A qué mascota pertenece; si se borra la mascota, sus vacunas también
            $table->string('nombre_vacuna', 100);                          // Nombre de la vacuna (ej: "Rabia", "Moquillo")
            $table->date('fecha_administracion');                          // Cuándo se puso la vacuna
            $table->date('proxima_dosis')->nullable();                     // Cuándo toca la siguiente — opcional
            // Sin timestamps() porque no necesitamos saber cuándo se creó el registro, solo las fechas médicas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_vaccines'); // Borra la tabla si deshacemos la migración
    }
};
