<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;

class Minister extends Model implements TaggableInterface
{
    use SoftDeletes,TaggableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

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
