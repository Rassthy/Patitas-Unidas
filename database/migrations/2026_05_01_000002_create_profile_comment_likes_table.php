<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_comment_likes', function (Blueprint $table) {
            $table->foreignId('comment_id')->constrained('profile_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->primary(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_comment_likes');
    }
};