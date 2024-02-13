<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'image',
        'title',
        'subtitle',
        'button_url',
        'button_label',
        'slide_order',
    ];
    protected $appends = [
        'image_url'
    ];

    public function getUpdateUrlAttribute()
    {
        return route('admin.home-slider.update_slide', $this->id);
    }
    public function getImageUrlAttribute()
    {
        return storage_url($this->image);
    }
}
