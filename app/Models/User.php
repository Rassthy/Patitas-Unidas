<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Pet;
use App\Models\Post;
use App\Models\Notification;
use App\Models\Report;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }
}
