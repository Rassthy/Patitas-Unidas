<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ForumCategory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'category_id',
        'author_id',
        'titulo',
        'descripcion',
        'animal_nombre',
        'animal_especie',
        'animal_raza',
        'provincia',
        'ciudad',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }
}
