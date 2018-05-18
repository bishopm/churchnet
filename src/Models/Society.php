<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function services()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Service');
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }
}
