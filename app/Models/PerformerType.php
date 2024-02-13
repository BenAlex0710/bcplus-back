<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformerType extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'performer_type_id'
    ];

    public function option()
    {
        return $this->hasOne('App\Models\SettingOption', 'id', 'performer_type_id');
    }
}
