<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phonetoken', 'individual_id', 'phone', 'google_id', 'facebook_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'individual_id' => 'integer',
        'level' => 'integer'
    ];

    public function individual()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Individual');
    }

    public function circuits()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\Circuit', 'permissible')->withPivot('permission');
    }

    public function societies()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\Society', 'permissible')->withPivot('permission');
    }

    public function districts()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\District', 'permissible')->withPivot('permission');
    }

    public function denominations()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\Denomination', 'permissible')->withPivot('permission');
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function routeNotificationForSlack($notification)
    {
        return env('LOG_SLACK_WEBHOOK_URL');
    }
}
