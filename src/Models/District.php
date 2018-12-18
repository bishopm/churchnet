<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = array('id');

    public function circuits()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Circuit');
    }

    public function settings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Setting', 'relatable');
    }

    public function feeditems()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Feeditem', 'distributable');
    }

    public function users()
    {
        return $this->morphToMany('Bishopm\Churchnet\Models\User', 'permissible');
    }

    public function chats()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Chat', 'chatable');
    }

    public function meetings()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Meeting', 'meetable');
    }
}
