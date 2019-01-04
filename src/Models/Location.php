<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = array('id');

    public function locatable()
    {
        return $this->morphTo();
    }

}
