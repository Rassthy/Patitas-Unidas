<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_comment_id')->nullable()->constrained('profile_comments')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->nullable()->check('rating >= 1 AND rating <= 5');
            $table->text('comentario');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_comments');
    }
};