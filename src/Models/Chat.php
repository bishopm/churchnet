<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Chat extends Model
{
    protected $guarded = array('id');

    public function chatable()
    {
        return $this->morphMany();
    }
}
