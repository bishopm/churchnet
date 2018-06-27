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
}
