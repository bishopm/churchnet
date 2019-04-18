<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Synod extends Model
{
    protected $guarded = array('id');

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function documents()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Document');
    }

    public function agendaitems()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Meeting', 'meetable');
    }
}
