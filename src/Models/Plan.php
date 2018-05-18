<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = array('id');

    public function tag()
    {
        return $this->hasOne('Bishopm\Churchnet\Models\Tag');
    }
}
