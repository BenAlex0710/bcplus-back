<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLiveData extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'resource_id', 'sid', 'uid', 'video_url'
    ];

    protected $appends = [
        'video_url'
    ];

    public function getVideoUrlAttribute()
    {
        $event = $this->event()->first();
        $t = date('d-m-Y', strtotime($event->start_time));
        $day = strtolower(date("d", strtotime($t)));
        $month = strtolower(date("m", strtotime($t)));
        $year = strtolower(date("Y", strtotime($t)));

        $fixedTitle = $event->id . $event->title;
        $folderName = $month . $day . $year . preg_replace('/[^\da-z]/i', '', $fixedTitle);

        return 'https://bcplusnews-live-streaming.s3.amazonaws.com/' . $folderName . '/' . $this->sid . '_' . $event->agora_channel_name . '.m3u8';
    }

    public function event()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }
}
