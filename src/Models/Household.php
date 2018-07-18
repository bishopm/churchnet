<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $guarded = array('id');

    public function individuals()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Individual')->orderBy('firstname');
    }
}
