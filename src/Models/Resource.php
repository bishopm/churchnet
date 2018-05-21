<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Actuallymab\LaravelComment\Commentable;

class Resource extends Model implements TaggableInterface
{
    use TaggableTrait, Commentable;
    
    protected $guarded = array('id');
}
