<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Model;

class Preacher extends Model
{
    use SoftDeletes, Taggable;

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
