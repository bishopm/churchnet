<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = array('id');

    public function relatable()
    {
        return $this->morphTo();
    }
}
