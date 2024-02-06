<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'amount',
        'admin_commission',
        'total_amount',
        'payment_status',
        'payment_response',
    ];

    protected $appends = [
        'formated_amount',
        'formated_admin_commission',
        'formated_total_amount',
    ];

    public function getFormatedAmountAttribute()
    {
        return format_amount($this->amount);
    }

    public function getFormatedTotalAmountAttribute()
    {
        return format_amount($this->total_amount);
    }

    public function getFormatedAdminCommissionAttribute()
    {
        return format_amount($this->admin_commission);
    }

    public function getPaymentStatusLabelAttribute()
    {
        return __('attendees_report.payment_status_labels.' . $this->payment_status);
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function event()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }
}
