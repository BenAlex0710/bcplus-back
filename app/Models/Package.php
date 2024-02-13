<?php

namespace App\Models;

use App\Traits\TranslateColums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory, TranslateColums;

    protected $fillable = [
        'price',
        'type',
        'validity',
        'status',
        'events',
        'event_max_duration',
        'max_guests',
        'max_attendees',
        'ticket_commission',
        'storage',
        'video_quality',
        'trial'
    ];

    protected $translateable_fields = [
        'name'
    ];

    protected $appends = [
        'formated_price'
    ];

    public function getFormatedPriceAttribute()
    {
        return format_amount($this->price);
    }

    public function scopeEnabled($query)
    {
        $query->where(
            'status',
            '1'
        );
    }

    public function getNameAttribute()
    {
        if (in_array($this->request_lang, $this->langs)) {
            return $this->{$this->request_lang . '_name'};
        }
        return $this->en_name;
    }
}
