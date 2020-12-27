<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;

class Venuebooking extends Model
{
    use Taggable;

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
