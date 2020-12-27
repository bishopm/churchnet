<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $guarded = array('id');

    public function service()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Service');
    }

}
