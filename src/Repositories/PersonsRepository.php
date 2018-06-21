<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PersonsRepository extends EloquentBaseRepository
{
    public function find($id)
    {
        return $this->model->with('preacher', 'positions')->find($id);
    }

    public function preachers()
    {
        return $this->model->with('preacher', 'positions')->get();
    }
}