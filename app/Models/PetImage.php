<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasFactory;

    protected $table = 'pet_images';

    protected $fillable = ['pet_id', 'url', 'orden'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}