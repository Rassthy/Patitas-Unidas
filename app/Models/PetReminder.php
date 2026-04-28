<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;

class PetReminder extends Model
{
    use HasFactory;

    protected $table = 'pet_reminders';

    protected $fillable = [
        'pet_id',
        'titulo',
        'mensaje',
        'fecha_alarma',
        'notificado',
    ];

    protected $casts = [
        'fecha_alarma' => 'datetime',
        'notificado' => 'boolean',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
