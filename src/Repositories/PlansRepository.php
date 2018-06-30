<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class PlansRepository extends EloquentBaseRepository
{
    public function latestplan($circuit)
    {
        return $this->model->where('circuit_id', $circuit)->orderBy('planyear', 'planmonth', 'planday')->get()->take(1);
    }

    public function preachingmonth($circuit, $yy, $mm)
    {
        return $this->model->with('person')->where('circuit_id', $circuit)->where('planyear', $yy)->where('planmonth', $mm)->orderBy('planyear', 'planmonth', 'planday')->get();
    }
}
