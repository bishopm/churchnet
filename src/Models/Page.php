<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
// use BeyondCode\Comments\Traits\HasComments;

class Page extends Model
{
    use Taggable;

    protected $guarded = array('id');
}
