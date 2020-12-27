<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{

    protected $guarded = array('id');

    public function circuit(){
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function society(){
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

}
