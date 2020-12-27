<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function statistics()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Statistic');
    }

    public function plans()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Plan');
    }
}
