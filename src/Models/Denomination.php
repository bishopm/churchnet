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

}
