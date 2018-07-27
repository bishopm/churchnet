<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Feeditem extends Model
{
    protected $guarded = array('id');

    public function distributable()
    {
        return $this->morphMany();
    }

    public function feedpost()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Feedpost');
    }
}
