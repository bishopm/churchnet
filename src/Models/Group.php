<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Bishopm\Churchnet\Models\Individual;

class Group extends Model
{
    protected $guarded = array('id');
    protected $casts = [
        'leader' => 'integer',
        'society_id' => 'integer'
    ];

    public function individuals()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Individual')->whereNull('group_individual.deleted_at')->withTimestamps()->orderBy('surname')->orderBy('firstname');
    }

    public function pastmembers()
    {
        return $this->belongsToMany('Bishopm\Churchnet\Models\Individual')->whereNotNull('group_individual.deleted_at')->withTimestamps()->withPivot('deleted_at');
    }

    public function chats()
    {
        return $this->morphMany('Bishopm\Churchnet\Models\Chat', 'chatable');
    }

    public function rostergroups()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Rostergroup');
    }

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }
}
