<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ForumCategory;
use App\Models\PostImage;
use App\Models\PostComment;
use App\Models\PostLike;

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

    protected $appends = ['likes_count', 'comments_count'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('post_images.orden');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_comment_id')->with('replies.user');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }
}
