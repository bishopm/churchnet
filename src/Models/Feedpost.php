<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Feedpost extends Model
{
    protected $guarded = array('id');

    public function feeditems()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Feeditem');
    }
}
