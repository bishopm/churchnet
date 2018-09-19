<?php

namespace Bishopm\Churchnet\Models;

use Cviebrock\EloquentTaggable\Models\Tag;

class Tagg extends Tag
{
    protected $guarded = array('id');

    public function scopeType($query, $type)
    {
        return $query->where('type',$type);
    }

}
