<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Chat extends Model
{
    protected $guarded = array('id');

    public function chatable()
    {
        return $this->morphMany();
    }

    public function scopeThisweek (Builder $query) {
        $monday = date("Y-m-d", strtotime('Monday this week'));
        $nextmonday = date("Y-m-d", strtotime('Monday next week'));
        return $query->where('publicationdate','>=', $monday)->where('publicationdate','<', $nextmonday);
    }
}
