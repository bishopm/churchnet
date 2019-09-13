<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $guarded = array('id');
    protected $casts = [
        'pgnumber' => 'integer',
        'society_id' => 'integer'
    ];
}
