<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function rostergroups()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Rostergroup');
    }

}
