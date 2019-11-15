<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $guarded = array('id');

    public function venuebookings()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Venuebooking');
    }
}
