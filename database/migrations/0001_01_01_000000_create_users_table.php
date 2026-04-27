<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('dni_nie', 15)->unique();
            $table->string('telefono', 20)->unique();
            $table->string('email', 150)->unique();
            $table->string('password_hash');
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->enum('tipo', ['usuario', 'protectora', 'organizacion', 'admin'])->default('usuario');
            $table->text('descripcion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('foto_perfil')->nullable();
            $table->string('banner')->nullable();
            $table->string('provincia', 50)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->boolean('email_verificado')->default(false);
            $table->boolean('telefono_verificado')->default(false);
            $table->boolean('activo')->default(true);
            $table->string('motivo_baja')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
