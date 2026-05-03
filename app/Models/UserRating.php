<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRating extends Model
{
    protected $fillable = ['user_id', 'voter_id', 'puntuacion', 'comentario'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}