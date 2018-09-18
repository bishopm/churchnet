<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Rostergroup extends Model
{
    protected $guarded = array('id');

    public function group()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Group');
    }

    public function roster()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Roster');
    }

    public function rosteritems()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Rosteritem');
    }
}
