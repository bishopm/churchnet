<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PositionsRepository extends EloquentBaseRepository
{
    public function all()
    {
        return $this->model->orderBy('position')->get();
    }
}