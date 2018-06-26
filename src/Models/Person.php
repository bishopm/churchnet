<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes, HasTags;

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
