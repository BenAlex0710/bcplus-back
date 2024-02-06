<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'performer_id',
        'reviewer_id',
        'stars',
        'review',
        'status',
    ];

    public function reviewer()
    {
        return $this->hasOne('App\Models\User', 'id', 'reviewer_id');
    }


    public function event()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }
}
