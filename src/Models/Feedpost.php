<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Feedpost extends Model
{
    protected $guarded = array('id');

    public function feeditem()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Feeditem');
    }
}
