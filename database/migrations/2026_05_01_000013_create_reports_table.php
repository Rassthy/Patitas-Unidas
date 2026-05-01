<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('tipo_entidad', ['perfil', 'post', 'post_comentario', 'perfil_comentario', 'mensaje_chat']);
            $table->unsignedBigInteger('entidad_id');
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'en_revision', 'aceptado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};