<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function meetable()
    {
        return $this->morphTo();
    }

    public function scopeCircuitmeeting($query, $circuit)
    {
        return $query->where('meetable_type','Bishopm\\Churchnet\\Models\\Circuit')->where('meetable_id',$circuit);
    }
}
