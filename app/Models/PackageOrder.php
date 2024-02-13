<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'events',
        'status',
        'start_date',
        'expiry_date',
        'payment_status',
        'payment_response',
        'package_data'
    ];


    protected $appends = [
        'payment_status_label',
        'formated_amount'
    ];

    public function getFormatedAmountAttribute()
    {
        return format_amount($this->amount);
    }
    public function getStatusLabelAttribute()
    {
        return __('package.orders.status_labels.' . $this->status);
    }

    public function getPaymentStatusLabelAttribute()
    {
        return __('package.payment_status.' . $this->payment_status);
    }

    public function getPackageDataAttribute($value)
    {
        return json_decode($value);
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
