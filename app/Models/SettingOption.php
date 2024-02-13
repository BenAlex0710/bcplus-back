<?php

namespace App\Models;

use App\Traits\TranslateColums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingOption extends Model
{
    use HasFactory, TranslateColums;

    // only write not translate able columns
    protected $fillable = [
        'field', 'status'
    ];

    //write the column name without language prefix
    protected $translateable_fields = [
        'name'
    ];

    public function getNameAttribute()
    {
        if (in_array($this->request_lang, $this->langs)) {
            return $this->{$this->request_lang . '_name'};
        }
        return $this->en_name;
    }
}
