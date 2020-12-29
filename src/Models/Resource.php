<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
// use BeyondCode\Comments\Traits\HasComments;

class Resource extends Model
{
    use Taggable;

    protected $canBeRated = false;
    protected $mustBeApproved = false;
    protected $guarded = array('id');
}
