<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'status', 'user_id', 'email', 'full_name'
    ];


    public function user()
    {
        return $this->hasOne('App\Models\User', 'email', 'email');
    }

    public function event()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }
}
