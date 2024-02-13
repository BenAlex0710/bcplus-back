<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'message',
        'parent_comment_id'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Models\EventComment', 'parent_comment_id');
    }
}
