<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $guarded = array('id');

    public function societies()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\Society','feedable');
    }

    public function circuits()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\Circuit','feedable');
    }

    public function districts()
    {
        return $this->morphedByMany('Bishopm\Churchnet\Models\District','feedable');
    }
    
}
