<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    protected $guarded = array('id');

    public function household()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Household');
    }

    public function groups()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Group');
    }
}
