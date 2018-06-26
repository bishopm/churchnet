<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Actuallymab\LaravelComment\Commentable;

class Resource extends Model
{
    use HasTags, Commentable;
    
    protected $guarded = array('id');
}
