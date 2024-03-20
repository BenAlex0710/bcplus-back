<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{


    protected $table = "posts";
    protected $primaryKey = "post_id";

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
}
