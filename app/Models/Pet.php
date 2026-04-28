<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
}
