<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PositionsRepository extends EloquentBaseRepository
{
    public function all()
    {
        return $this->model->orderBy('position')->get();
    }

    public function identify($circuit, $position)
    {
        $position = $this->model->with('persons')->where('position', urldecode($position))->first();
        $persons=array();
        if (count($position->persons)) {
            foreach ($position->persons as $person) {
                if ($person->circuit_id==$circuit) {
                    $persons[]=$person->title . " " . substr($person->firstname, 0, 1) . " " . $person->surname . " (" . $person->phone . ")";
                }
            }
        } else {
            $persons[]="";
        }
        return $persons;
    }
}
