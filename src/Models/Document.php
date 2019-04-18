<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = array('id');

    public function synod()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Synod');
    }

}
