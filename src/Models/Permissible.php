<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Permissible extends Model
{
    protected $guarded = array('id');
    protected $casts = [
        'user_id' => 'integer',
        'permissible_id' => 'integer'
    ];
}
