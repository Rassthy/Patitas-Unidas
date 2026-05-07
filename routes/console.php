<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Enviar notificaciones de recordatorios de mascotas cada hora
// Cambiado temporalmente para pruebas
Schedule::command('pets:send-reminders')->everyMinute();