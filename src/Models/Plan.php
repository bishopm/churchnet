<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = array('id');

    public function label()
    {
        return $this->hasOne('Bishopm\Churchnet\Models\Label');
    }

    public function preacher()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Preacher');
    }
}
