<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PeopleRepository extends EloquentBaseRepository
{
    public function preachers()
    {
        return $this->model->where('status', 'preacher')->get();
    }
}
