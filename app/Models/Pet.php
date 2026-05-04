<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// El Modelo es la representación en PHP de la tabla 'pets' de la base de datos
class Pet extends Model
{
    // $fillable lista qué columnas se pueden rellenar de forma masiva (por seguridad,
    // si no lo listas aquí, Laravel no te deja guardarlo de golpe)
    protected $fillable = [
        'user_id',
        'nombre',
        'especie',
        'raza',
        'edad',
        'descripcion',
        'foto',
    ];

    // "Una mascota PERTENECE A un usuario" — nos permite hacer $pet->user y obtener el dueño
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // "Una mascota TIENE MUCHAS vacunas" — nos permite hacer $pet->vaccines y obtener la lista
    public function vaccines()
    {
        return $this->hasMany(PetVaccine::class);
    }

    // "Una mascota TIENE MUCHOS recordatorios", ordenados por fecha para que salgan en orden cronológico
    public function reminders()
    {
        return $this->hasMany(PetReminder::class)->orderBy('fecha_alarma');
    }
}
