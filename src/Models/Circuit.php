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

    public function feeds()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\Feed', 'feedable');
    }

    public function location()
    {
        return $this->morphOne('Bishopm\Churchnet\Models\Location', 'locatable');
    }

    public function district()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\District');
    }

    public function people()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Person', 'personable');
    }

    public function leaders()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Person', 'personable')->where('active', 'yes')->with('individual')->whereHas('individual');
    }

    public function preachers()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Person')->where('active', 'yes')->where('status', 'preacher')->with('individual')->
        whereHas('individual');
    }

    public function ministers()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Person', 'personable')->where('status', 'minister')->with('individual', 'tags')
        ->whereHas('individual');
    }

    public function tagged($tag)
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Person', 'personable')->withAnyTags($tag);
    }

    public function meetings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Meeting', 'meetable');
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
