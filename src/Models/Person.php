<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes, Taggable;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }
}
