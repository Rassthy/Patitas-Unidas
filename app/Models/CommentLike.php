<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    public $timestamps = false;

    protected $table = 'post_comment_likes';

    protected $fillable = ['comment_id', 'user_id'];
}