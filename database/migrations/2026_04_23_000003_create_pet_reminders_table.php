<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla para recordatorios del dueño (cita vet, medicación, etc.)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_reminders', function (Blueprint $table) {
            $table->id();                                                  // ID único del recordatorio
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete(); // De qué mascota es el recordatorio
            $table->string('titulo', 100);                                 // Título corto (ej: "Revisión anual")
            $table->text('mensaje')->nullable();                           // Descripción más larga — opcional
            $table->dateTime('fecha_alarma');                              // Fecha Y hora exacta del recordatorio
            $table->boolean('notificado')->default(false);                 // Marca si ya se avisó al usuario (de momento siempre false)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_reminders');
    }
};
