<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $fillable = [
        'post_id',
        'author_id',
        'parent_comment_id',
        'comentario',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'parent_comment_id');
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_comment_id');
    }

    public function likes()
    {
    return $this->hasMany(CommentLike::class, 'comment_id');
    }

    public function isLikedBy($userId)
    {
    return $this->likes()->where('user_id', $userId)->exists();
    }
}