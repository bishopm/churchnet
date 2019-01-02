<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Specialday extends Model
{

    protected $guarded = array('id');

    public function household(){
        return $this->belongsTo('Bishopm\Churchnet\Models\Household');
    }

    public function scopeInsociety($query, $society)
    {
        return $query->join('households', 'households.id', '=', 'household_id')->whereHas('household', function ($q) use ($society) {
            $q->where('society_id', $society);
        })->orderBy('details', 'ASC');
    }
}
