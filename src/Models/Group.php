<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = array('id');

    public function individuals()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Individual');
    }
}