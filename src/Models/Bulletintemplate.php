<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bulletintemplate extends Model
{
    protected $guarded = array('id');

    public function bulletintemplateitems()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Bulletintemplateitem');
    }
}
