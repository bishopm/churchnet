<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bulletinitem extends Model
{
    protected $guarded = array('id');

    public function bulletin()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Bulletin');
    }
}
