<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TimeConversion implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        // return $value;
        $utc_time = Carbon::parse($value, 'UTC');
        $time = Carbon::parse($value, $attributes['timezone']);
        $difference = $utc_time->diffInSeconds($time);
        $dt = new Carbon($value);
        $dt->addSeconds($difference);
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        $utc_time = Carbon::parse($value, 'UTC');
        $time = Carbon::parse($value, request()->timezone);
        $difference = $utc_time->diffInSeconds($time);
        $dt = new Carbon($value);
        $dt->subSeconds($difference);
        return $dt->format('Y-m-d H:i:s');
    }
}
