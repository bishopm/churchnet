<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletinwidgetfield extends Model
{
    protected $guarded = array('id');

    public function bulletinwidget()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Bulletinwidget');
    }
}
