<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $guarded = array('id');

    public function plan()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Plan');
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }
}
