<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Dailyreading extends Model
{
    protected $guarded = array('id');
    protected $casts = [
        'readingday' => 'integer',
        'readingplan_id' => 'integer'
    ];
    
    public function readingplan()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Readingplan');
    }
}
