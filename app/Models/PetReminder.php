<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla 'pet_reminders'
class PetReminder extends Model
{
    // Sin timestamps en esta tabla
    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'titulo',
        'mensaje',
        'fecha_alarma',
        'notificado',
    ];

    protected $casts = [
        'fecha_alarma' => 'datetime', // Convierte a Carbon con fecha + hora (no solo fecha)
        'notificado'   => 'boolean',  // Lo trata como true/false en PHP (en BD es 0 o 1)
    ];

    // "Un recordatorio PERTENECE A una mascota"
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
