<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    protected $guarded = array('id');

    public function individual()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Individual');
    }

    public function scopeThisweek(Builder $query)
    {
        $monday = date("Y-m-d H:i:s", strtotime('Monday this week'));
        $nextmonday = date("Y-m-d H:i:s", strtotime('Monday next week'));
        return $query->where('created_at', '>=', $monday)->where('created_at', '<', $nextmonday);
    }
}
