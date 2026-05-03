<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations
    public function up()
    {
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade'); 
            $table->decimal('puntuacion', 2, 1);
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('user_ratings');
    }
};
