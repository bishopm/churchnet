<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class PlansRepository extends EloquentBaseRepository
{
    public function latestplan($circuit)
    {
        return $this->model->where('circuit_id', $circuit)->orderBy('planyear', 'planmonth', 'planday')->get()->take(1);
    }
}
