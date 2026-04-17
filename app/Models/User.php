<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'dni_nie',
        'telefono',
        'email',
        'password_hash',
        'nombre',
        'apellidos',
        'tipo',
        'descripcion',
        'fecha_nacimiento',
        'foto_perfil',
        'banner',
        'provincia',
        'ciudad',
        'email_verificado',
        'telefono_verificado',
        'activo',
        'motivo_baja',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'email_verificado' => 'boolean',
        'telefono_verificado' => 'boolean',
        'activo' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}