<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $guarded = array('id');

    public function individual(){
        return $this->belongsTo('Bishopm\Churchnet\Models\Individual');
    }

}
