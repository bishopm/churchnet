<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = array('id');

    public function circuits()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Circuit');
    }

    public function feeds()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\Feed', 'feedable');
    }

    public function location()
    {
        return $this->morphOne('Bishopm\Churchnet\Models\Location', 'locatable');
    }

    public function individuals()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Individual')->withPivot('description')->orderBy('display_order','ASC');
    }

    public function denomination()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Denomination');
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

    public function meetings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Meeting', 'meetable');
    }

    public function people()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Person', 'personable');
    }
}
