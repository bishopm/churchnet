<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Preacher extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }
    
    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }
}
