<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Feeditem extends Model
{
    protected $guarded = array('id');

    public function distributable()
    {
        return $this->morphMany();
    }

    public function feedpost()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Feedpost');
    }

    public function scopeMonday (Builder $query, $monday) {
        return $query->whereHas('feedpost', function ($q) use ($monday) {
                $q->where('publicationdate', $monday);
        });
    }
}
