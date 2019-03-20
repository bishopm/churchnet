<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $guarded = array('id');

    public function individuals()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Individual')->whereNull('deleted_at')->orderBy('firstname');
    }

    public function scopeMembers($query, $society)
    {
        return $query->where('society_id', $society)->whereHas('individuals', function ($q) {
            $q->where('memberstatus', '=', 'member');
        })->with('individuals')->get();
    }

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function pastorals()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Pastoral')->orderBy('pastoraldate', 'DESC');
    }

    public function specialdays()
    {
        return $this->hasMany('Bishopm\Churchnet\Models\Specialday')->orderBy('anniversarydate', 'DESC');
    }

    public function location()
    {
        return $this->morphOne('Bishopm\Churchnet\Models\Location', 'locatable');
    }
}
