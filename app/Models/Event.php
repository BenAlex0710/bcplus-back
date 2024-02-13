<?php

namespace App\Models;

use App\Casts\TimeConversion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'joining_fee',
        'start_time',
        'end_time',
        'live_status',
        'agora_channel_name',
        'timezone',
        'event_type_id',
        'banner',
        'package_order_id',
        'description',
        'notification'
    ];

    protected $appends = [
        'event_type',
        'upcoming',
        'slug',
        'rating',
        'duration',
        'joining_fee_formated',
        'is_saved'
    ];

    protected $casts = [
        'start_time' => TimeConversion::class,
        'end_time' => TimeConversion::class,
    ];


    public function getJoiningFeeFormatedAttribute()
    {
        return format_amount($this->joining_fee);
    }

    public function getIsSavedAttribute()
    {
        $auth_user = request()->user('api');
        if ($auth_user) {
            $saved = $auth_user->saved_events()
                ->where('event_id', $this->id)
                ->first();
            // dd($saved);
            if ($saved) {
                return true;
            }
        }
        return false;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now('UTC')->format('Y-m-d H:i:s'));
    }

    public function getUpcomingAttribute()
    {
        $start_time = Carbon::parse($this->start_time, $this->timezone);
        $now = Carbon::now($this->timezone);
        // return [
        //     'now' =>  $now->format('Y-m-d H:i:s'),
        //     'start_time' =>  $start_time->format('Y-m-d H:i:s'),
        //     'start' => $start_time->diffInMinutes($now, false),
        //     'no' => $now->diffInMinutes($start_time, false),
        // ];
        if ($start_time->greaterThan($now)) {
            return true;
        }
        return false;
    }

    public function getDurationAttribute()
    {
        $end_time = Carbon::parse($this->end_time);
        $start_time = Carbon::parse($this->start_time);
        $diff_hour = $end_time->diffInHours($start_time);
        $diff_min = $end_time->diffInMinutes($start_time);
        $mins = $diff_min - ($diff_hour * 60);
        $diff_seconds = $end_time->diffInSeconds($start_time);
        $seconds = $diff_seconds - ($diff_min * 60);
        return str_pad($diff_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->title, '-');
    }

    public function getRatingAttribute()
    {
        return $this->reviews()->avg('stars') ?: 0;
    }

    public function getEventTypeAttribute()
    {
        $option = SettingOption::find($this->event_type_id);
        return $option->name;
    }

    public function getBannerAttribute($value)
    {
        return !empty($value) ? storage_url($value) : null;
    }

    public function live_data()
    {
        return $this->hasOne('App\Models\EventLiveData');
    }

    public function performer()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\EventComment')->where('parent_comment_id', '0');
    }

    public function attendees()
    {
        return $this->hasMany('App\Models\EventAttendee');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\EventReview');
    }

    public function package_data()
    {
        return $this->hasOne('App\Models\PackageOrder', 'id', 'package_order_id');
    }

    public function guests()
    {
        return $this->hasMany('App\Models\EventGuest');
    }
}
