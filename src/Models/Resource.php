<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use Actuallymab\LaravelComment\Commentable;

class Resource extends Model
{
    use Taggable, Commentable;

    protected $canBeRated = false;    
    protected $guarded = array('id');
}
