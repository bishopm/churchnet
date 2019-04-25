<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bulletin extends Model
{
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function bulletinitems()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Bulletinitem');
    }
}
