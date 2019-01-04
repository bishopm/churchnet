<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    protected $guarded = array('id');

    public function districts()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\District');
    }

    public function circuits()
    {
        return $this->hasManyThrough('Bishopm\Churchnet\Models\Circuit','Bishopm\Churchnet\Models\District');
    }

    public function individuals()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Individual')->withPivot('description')->orderBy('display_order','ASC');
    }

    public function location()
    {
        return $this->morphOne('Bishopm\Churchnet\Models\Location', 'locatable');
    }

}
