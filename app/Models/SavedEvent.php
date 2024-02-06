<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedEvent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id'];

    public function event_info()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }
}
