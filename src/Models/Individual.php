<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;

class Individual extends Model
{
    use Taggable;

    protected $guarded = array('id');

    public function household()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Household');
    }

    public function groups()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Group');
    }

    public function user()
    {
        return $this->hasone('Bishopm\Churchnet\Models\User');
    }

    public function person()
    {
        return $this->hasone('Bishopm\Churchnet\Models\Person');
    }

    public function scopeSocietymember($query, $societies)
    {
        return $query->whereHas('household', function ($q) use ($societies) {
            $q->whereIn('society_id', $societies);
        })->orderBy('surname', 'ASC')->orderBy('firstname', 'ASC');
    }

    public function getFullnameAttribute()
    {
        return $this->firstname . " " . $this->surname;
    }
}
