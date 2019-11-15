<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Venuebooking extends Model
{
    protected $guarded = array('id');

    public function venue()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Venue');
    }

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }
}
