<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;

class Preacher extends Model
{
    use SoftDeletes, HasTags;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function person()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Person');
    }
    
    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function plans()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Plan');
    }
}
