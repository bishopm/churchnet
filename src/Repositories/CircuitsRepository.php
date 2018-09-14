<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Person;

class CircuitsRepository extends EloquentBaseRepository
{
    public function withsocieties($id)
    {
        return $this->model->with('societies')->where('id',$id)->first();
    }

    public function preachers($id)
    {
        $persons = Person::join('individuals', 'individuals.id','=','people.individual_id')->where('circuit_id',$id)->where('status','preacher')->orderBy('individuals.surname')->get();
        return $persons;
    }
}
