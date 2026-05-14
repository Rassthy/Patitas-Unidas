<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->string('titulo', 100);
            $table->text('mensaje')->nullable();
            $table->dateTime('fecha_alarma');
            $table->string('timezone')->default('Europe/Madrid');
            $table->boolean('notificado')->default(false);
            $table->json('stages_notified')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_reminders');
    }
};