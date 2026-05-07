<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_reminders', function (Blueprint $table) {
            $table->json('stages_notified')->nullable()->after('notificado');
        });
    }

    public function down(): void
    {
        Schema::table('pet_reminders', function (Blueprint $table) {
            $table->dropColumn('stages_notified');
        });
    }
};