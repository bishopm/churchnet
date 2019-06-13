<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Readingplan extends Model
{
    protected $guarded = array('id');

    public function dailyreadings()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Dailyreading');
    }
}
