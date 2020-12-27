<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bulletinwidget extends Model
{
    protected $guarded = array('id');

    public function bulletinwidgetfield()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Bulletinwidgetfield');
    }
}
