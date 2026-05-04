<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla 'pet_vaccines'
class PetVaccine extends Model
{
    // Le decimos a Laravel que esta tabla NO tiene columnas created_at/updated_at
    // (no las creamos en la migración, así que hay que avisarle o dará error)
    public $timestamps = false;

    protected $fillable = [
        'pet_id',
        'nombre_vacuna',
        'fecha_administracion',
        'proxima_dosis',
    ];

    // Los $casts convierten automáticamente esas columnas de texto a objetos Carbon (fechas)
    // Así puedes hacer $vaccine->proxima_dosis->format('d/m/Y') o ->isPast() directamente
    protected $casts = [
        'fecha_administracion' => 'date',
        'proxima_dosis'        => 'date',
    ];

    // "Una vacuna PERTENECE A una mascota"
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
