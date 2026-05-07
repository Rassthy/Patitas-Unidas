<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\PetReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendPetReminderNotifications extends Command
{
    protected $signature   = 'pets:send-reminders';
    protected $description = 'Send notifications for upcoming pet reminders (5d, 2d, 12h, 1h before)';

    // Stages: key => [label, minutes_before, message_template]
    private array $stages = [
        '5d'  => [5 * 24 * 60,  'faltan 5 días para'],
        '2d'  => [2 * 24 * 60,  'faltan 2 días para'],
        '12h' => [12 * 60,      'faltan 12 horas para'],
        '1h'  => [60,           'falta 1 hora para'],
        '5m'  => [5,            'faltan 5 minutos para'],
        '3m'  => [3,            'faltan 3 minutos para'],
        '2m'  => [2,            'faltan 2 minutos para'],
        '1m'  => [1,            'falta 1 minuto para'],
    ];

    public function handle(): void
    {
        $now = \Carbon\Carbon::now();

        $reminders = \App\Models\PetReminder::where('notificado', false)
            ->with('pet.user')
            ->get();

        foreach ($reminders as $reminder) {
            $stagesNotified = $reminder->stages_notified ?? [];
            
            $createdAt   = \Carbon\Carbon::parse($reminder->created_at);
            $fechaAlarma = \Carbon\Carbon::parse($reminder->fecha_alarma);

            foreach ($this->stages as $stageKey => [$stageMinutes, $stageLabel]) {
                if (in_array($stageKey, $stagesNotified)) continue;

                // Calculamos el momento exacto de la alerta
                $momentoAlerta = $fechaAlarma->copy()->subMinutes($stageMinutes);

                // ANTI-SPAM TOLERANTE: Le restamos 1 minuto a la fecha de creación.
                // Así, si tardaste unos segundos en crear la alerta, no te la silenciará por error.
                if ($createdAt->copy()->subMinutes(1)->greaterThan($momentoAlerta)) {
                    $stagesNotified[] = $stageKey;
                    continue;
                }

                // PRECISIÓN: Si ya hemos superado la hora de la alerta, se envía.
                if ($now->greaterThanOrEqualTo($momentoAlerta)) {
                    $this->sendNotification($reminder, $stageLabel);
                    $stagesNotified[] = $stageKey;
                }
            }

            $reminder->stages_notified = $stagesNotified;

            if (in_array('1m', $stagesNotified) || in_array('1h', $stagesNotified)) {
                $reminder->notificado = true;
            }

            $reminder->save();
        }

        $this->info('Recordatorios procesados: ' . $reminders->count());
    }

    private function sendNotification(PetReminder $reminder, string $stageLabel): void
    {
        if (!$reminder->pet || !$reminder->pet->user) {
            return;
        }

        Notification::create([
            'user_id'    => $reminder->pet->user_id,
            'tipo'       => 'recordatorio_mascota',
            'titulo'     => '🐾 Recordatorio: ' . $reminder->titulo,
            'mensaje'    => "¡Atención! {$stageLabel} el recordatorio \"{$reminder->titulo}\" de tu mascota {$reminder->pet->nombre}.",
            'enlace_url' => '/profile/' . $reminder->pet->user->username,
            'leida'      => false,
        ]);
    }
}