<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Feedable extends Model
{
    protected $guarded = array('id');

    public function feed()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Feed');
    }

}
