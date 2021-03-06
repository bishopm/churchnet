<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes, Taggable;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');
    protected $casts = [
        'circuit_id' => 'integer',
        'personable_id' => 'integer',
        'individual_id' => 'integer'
    ];

    public function personable()
    {
        return $this->morphTo();
    }

    public function circuit()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Circuit');
    }

    public function society()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Society');
    }

    public function individual()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Individual');
    }

    public function scopeDenomination($query, $slug)
    {
        return $query->join('circuits', 'circuits.id', '=', 'circuit_id')
                    ->join('districts', 'districts.id', '=', 'district_id')
                    ->join('denominations', 'denominations.id', '=', 'denomination_id')
                    ->where('denominations.slug', $slug)
                    ->where('status', 'minister')->select('people.*');
    }

    public function scopeDistrictministers($query, $id)
    {
        return $query->join('circuits', 'circuits.id', '=', 'personable_id')
                    ->join('districts', 'districts.id', '=', 'district_id')
                    ->where('status', 'minister')->select('people.*')
                    ->where('personable_type', 'Bishopm\Churchnet\Models\Circuit')->select('people.*')
                    ->where('districts.id', $id);
    }

    public function scopeDistrictdeacons($query, $id)
    {
        return $query->join('circuits', 'circuits.id', '=', 'personable_id')
                    ->join('districts', 'districts.id', '=', 'district_id')
                    ->where('status', 'deacon')->select('people.*')
                    ->where('personable_type', 'Bishopm\Churchnet\Models\Circuit')->select('people.*')
                    ->where('districts.id', $id);
    }

}
