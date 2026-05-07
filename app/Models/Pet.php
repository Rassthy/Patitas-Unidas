<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['foto_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PetImage::class)->orderBy('orden');
    }

    public function vaccines()
    {
        return $this->hasMany(PetVaccine::class);
    }

    public function reminders()
    {
        return $this->hasMany(PetReminder::class)->orderBy('fecha_alarma');
    }

    public function getFotoUrlAttribute(): string
    {
        if (!$this->foto) {
            return asset('img/defaults/foto_perfil_generica.png');
        }

        if (str_starts_with($this->foto, 'http')) {
            return $this->foto;
        }

        return asset('storage/' . $this->foto);
    }

    public function getMainPhotoUrl(): string
    {
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return asset('storage/' . $firstImage->url);
        }
        return $this->foto_url;
    }
}