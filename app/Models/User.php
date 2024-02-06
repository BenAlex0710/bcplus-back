<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'status',
        'email_verified_at',
        'photo',
        'banner',
        'role',
        'headline',
        'bio',
        'social_profiles',
        'organization_name',
        'organization_email',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'zip',
        'trial_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'rating',
        'role_label'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getActionAttribute()
    {
        $action = '';
        if ($this->status == '0') {
            $action .= '<a class="text-success change_status" href="' . route('admin.user.change_status', ['id' => $this->id, 'status' => '1']) . '" title="' . __('user.approve_user') . '"><i class="fe-check-circle"></i></a> &nbsp;';
        }
        if ($this->status == '2') {
            $action .= '<a class="text-warning change_status" href="' . route('admin.user.change_status', ['id' => $this->id, 'status' => '0']) . '" title="' . __('user.unapprove_user') . '"><i class="fe-x-circle"></i></a> &nbsp;';
        } else {
            $action .= '<a  class="text-danger change_status" href="' . route('admin.user.change_status', ['id' => $this->id, 'status' => '2']) . '" title="' . __('user.suspend_user') . '"><i class="fe-x-circle"></i></a>';
        }
        return $action;
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case '0':
                return '<span class="text-warning">' . __('user.unapproved') . '</span>';
                break;

            case '1':
                return '<span class="text-success">' . __('user.approved') . '</span>';
                break;

            case '2':
                return '<span class="text-danger">' . __('user.suspended') . '</span>';
                break;

            default:
                return '<span class="text-warning">' . __('user.unapproved') . '</span>';
                break;
        }
    }

    public function getRoleLabelAttribute()
    {
        switch ($this->role) {
            case '1':
                return __('user.role_1');
                break;

            case '2':
                return  __('user.role_2');
                break;

            default:
                return __('user.role_1');
                break;
        }
    }

    public function getRatingAttribute()
    {
        return $this->reviews()->avg('stars') ?: 0;
    }

    public function getPhotoAttribute($value)
    {
        return !empty($value) ? asset($value) : asset('admin/images/users/avatar-1.jpg');
    }

    public function getBannerAttribute($value)
    {
        return !empty($value) ? asset($value) : null;
    }

    public function package_orders()
    {
        return $this->hasMany('App\Models\PackageOrder');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\EventReview', 'performer_id');
    }

    public function event_invitations()
    {
        return $this->hasMany('App\Models\EventGuest', 'email', 'email');
    }

    public function support_messages()
    {
        return $this->hasMany('App\Models\SupportMessage', 'from');
    }

    public function saved_events()
    {
        return $this->hasMany('App\Models\SavedEvent');
    }

    public function performer_types()
    {
        return $this->hasMany('App\Models\PerformerType');
    }
}
