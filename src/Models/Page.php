<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use Actuallymab\LaravelComment\Commentable;

class Page extends Model
{
    use Taggable, Commentable;

    protected $guarded = array('id');
    protected $canBeRated = false;
    protected $mustBeApproved = false;
}
