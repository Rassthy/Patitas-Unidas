<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
        'user_settings',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'email_verificado' => 'boolean',
        'telefono_verificado' => 'boolean',
        'activo' => 'boolean',
        'user_settings' => 'array',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getFotoPerfilUrlAttribute()
    {
        if (!$this->foto_perfil || $this->foto_perfil === 'defaults/foto_perfil_generica.png' || $this->foto_perfil === 'storage/defaults/foto_perfil_generica.png') {
            return asset('img/defaults/foto_perfil_generica.png');
        }

        if (Str::startsWith($this->foto_perfil, ['http://', 'https://'])) {
            return $this->foto_perfil;
        }

        if (Str::startsWith($this->foto_perfil, 'img/')) {
            return asset($this->foto_perfil);
        }

        return asset('storage/' . $this->foto_perfil);
    }

    public function getBannerUrlAttribute()
    {
        if (!$this->banner) {
            return 'https://via.placeholder.com/1200x300/4CAF50/FFFFFF?text=Banner';
        }

        if (Str::startsWith($this->banner, ['http://', 'https://'])) {
            return $this->banner;
        }

        if (Str::startsWith($this->banner, 'img/')) {
            return asset($this->banner);
        }

        return asset('storage/' . $this->banner);
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
