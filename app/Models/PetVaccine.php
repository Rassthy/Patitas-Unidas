<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;

class PetVaccine extends Model
{
    use HasFactory;

    protected $table = 'pet_vaccines';

    protected $fillable = [
        'pet_id',
        'nombre_vacuna',
        'fecha_administracion',
        'proxima_dosis',
    ];

    protected $casts = [
        'fecha_administracion' => 'date',
        'proxima_dosis' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
