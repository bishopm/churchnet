<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Circuit extends Model
{
    protected $guarded = array('id');

    public function societies()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Society');
    }

    public function district()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\District');
    }

    public function people()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Person');
    }

    public function preachers()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Person')->where('status', 'preacher')->with('individual')->whereHas('individual');
    }

    public function meetings()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Meeting');
    }

    public function settings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Setting', 'relatable');
    }

    public function feeditems()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Feeditem', 'distributable');
    }

    public function users()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\User', 'permissible');
    }

    public function chats()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Chat', 'chatable');
    }
}
