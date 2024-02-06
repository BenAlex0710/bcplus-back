<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stars',
        'review',
        'reviewer_id',
        'status',
    ];

    public function reviewer()
    {
        return $this->hasOne('App\Models\User', 'id', 'reviewer_id');
    }
}
