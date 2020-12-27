<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Pastoral extends Model
{
    protected $guarded = array('id');

    public function household()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Household');
    }

    public function individual()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Individual');
    }
}
