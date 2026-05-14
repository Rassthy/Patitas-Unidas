<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\PetReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPetReminderNotifications extends Command
{
    protected $signature   = 'pets:send-reminders';
    protected $description = 'Send notifications for upcoming pet reminders at multiple intervals';

    private array $stages = [
        '5d'  => [5 * 24 * 60, 'faltan **5 días**',     24 * 60],
        '2d'  => [2 * 24 * 60, 'faltan **2 días**',     12 * 60],
        '12h' => [12 * 60,     'faltan **12 horas**',   3 * 60],
        '2h'  => [2 * 60,      'faltan **2 horas**',    60],

        '10m' => [10, 'faltan **10 minutos**', 5],
        '9m'  => [9,  'faltan **9 minutos**',  5],
        '8m'  => [8,  'faltan **8 minutos**',  5],
        '7m'  => [7,  'faltan **7 minutos**',  5],
        '6m'  => [6,  'faltan **6 minutos**',  5],
        '5m'  => [5,  'faltan **5 minutos**',  5],
        '4m'  => [4,  'faltan **4 minutos**',  5],
        '3m'  => [3,  'faltan **3 minutos**',  5],
        '2m'  => [2,  'faltan **2 minutos**',  5],
        '1m'  => [1,  'falta **1 minuto**',    5],

        '0m'  => [0,  '¡**el momento ha llegado**!', 10],
    ];

    public function handle(): void
    {
        $now = Carbon::now();
        $reminders = PetReminder::where('notificado', false)
            ->with('pet.user')
            ->get();

        $processed = 0;
        $sent      = 0;
        $skippedStale = 0;

        foreach ($reminders as $reminder) {
            $processed++;
            $stagesNotified = $reminder->stages_notified ?? [];
            $fechaAlarma    = Carbon::parse($reminder->fecha_alarma);
            $createdAt      = Carbon::parse($reminder->created_at);

            foreach ($this->stages as $stageKey => [$minutesBefore, $label, $staleWindowMinutes]) {

                if (in_array($stageKey, $stagesNotified, true)) {
                    continue;
                }

                $triggerAt = $fechaAlarma->copy()->subMinutes($minutesBefore);

                if ($createdAt->copy()->subSeconds(30)->greaterThan($triggerAt)) {
                    $stagesNotified[] = $stageKey;
                    continue;
                }

                $staleAt = $triggerAt->copy()->addMinutes($staleWindowMinutes);
                if ($now->greaterThan($staleAt)) {
                    $stagesNotified[] = $stageKey;
                    $skippedStale++;
                    continue;
                }

                if ($now->greaterThanOrEqualTo($triggerAt)) {
                    $this->sendNotification($reminder, $label, $fechaAlarma);
                    $stagesNotified[] = $stageKey;
                    $sent++;
                }
            }

            $reminder->stages_notified = $stagesNotified;

            if (in_array('0m', $stagesNotified, true)) {
                $reminder->notificado = true;
            }

            $reminder->save();
        }

        $this->info(
            "Recordatorios revisados: {$processed} | " .
            "Notificaciones enviadas: {$sent} | " .
            "Stages obsoletos descartados: {$skippedStale}"
        );
    }

    private function sendNotification(PetReminder $reminder, string $label, Carbon $fechaAlarma): void
    {
        if (! $reminder->pet || ! $reminder->pet->user) {
            return;
        }

        $petName      = $reminder->pet->nombre;
        $reminderName = $reminder->titulo;

        $tz = $reminder->timezone ?? 'Europe/Madrid';

        $fechaLocal = $fechaAlarma->copy()->setTimezone($tz);

        $fechaStr = $fechaLocal->locale('es')->translatedFormat('d/m/Y \a \l\a\s H:i');

        $mensaje = "¡Tu recordatorio **{$reminderName}** para tu mascota **{$petName}** "
                . "vence el {$fechaStr} — {$label}!";

        Notification::create([
            'user_id'    => $reminder->pet->user_id,
            'tipo'       => 'recordatorio_mascota',
            'titulo'     => "🐾 Recordatorio: {$reminderName}",
            'mensaje'    => $mensaje,
            'enlace_url' => '/profile/' . $reminder->pet->user->username,
            'leida'      => false,
        ]);
    }
}