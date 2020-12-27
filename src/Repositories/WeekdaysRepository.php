<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class WeekdaysRepository extends EloquentBaseRepository
{
    public function findfordate($circuit,$weekday)
    {
        return $this->model->where('circuit_id','=',$circuit)->where('servicedate','=',$weekday)->first();
    }
}
