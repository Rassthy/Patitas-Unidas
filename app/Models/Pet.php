<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PetVaccine;
use App\Models\PetReminder;

class Pet extends Model
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'user_id',
        'nombre',
        'especie',
        'raza',
        'edad',
        'descripcion',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vaccines()
    {
        return $this->hasMany(PetVaccine::class);
    }

    public function reminders()
    {
        return $this->hasMany(PetReminder::class)->orderBy('fecha_alarma');
    }
}
