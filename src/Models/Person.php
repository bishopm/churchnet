<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;

class Person extends Model implements TaggableInterface
{
    use SoftDeletes, TaggableTrait;

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

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }
}
