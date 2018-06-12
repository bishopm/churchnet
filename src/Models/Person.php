<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');
    protected $table = "persons";

    public function preacher()
    {
        return $this->hasOne('Bishopm\Churchnet\Models\Preacher');
    }
    
    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function positions()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Position');
    }
}
