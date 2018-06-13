<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $guarded = array('id');

    public function persons()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Person');
    }
}
