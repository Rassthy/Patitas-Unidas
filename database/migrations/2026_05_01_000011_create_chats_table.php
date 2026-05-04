<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_group')->default(false);
            $table->string('nombre_grupo', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            $table->boolean('is_admin')->default(false);
            $table->primary(['chat_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
        Schema::dropIfExists('chats');
    }
};