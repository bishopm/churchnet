<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function services()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Service');
    }

    public function location()
    {
        return $this->morphOne('Bishopm\Churchnet\Models\Location', 'locatable');
    }

    public function feeds()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\Feed', 'feedable');
    }

    public function rosters()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Roster');
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function feeditems()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Feeditem', 'distributable');
    }

    public function chats()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Chat', 'chatable');
    }

    public function users()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\User', 'permissible');
    }

    public function households()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Household');
    }
    
    public function meetings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Meeting', 'meetable');
    }
}
