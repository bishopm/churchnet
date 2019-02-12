<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Rosteritem extends Model
{
    protected $guarded = array('id');

    public function rostergroup()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Rostergroup');
    }

    public function scopeRosterdate($query, $date)
    {
        return $query->where('rosterdate',$date);
    }
}
