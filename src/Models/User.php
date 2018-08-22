<?php

namespace Bishopm\Churchnet\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phonetoken', 'individual_id', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
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
}
