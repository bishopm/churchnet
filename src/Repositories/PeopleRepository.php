<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PeopleRepository extends EloquentBaseRepository
{
    public function preachers()
    {
        $preachers = $this->model->where('status', 'preacher')->get();
        foreach ($preachers as $preacher) {
            $dum = $preacher->tags;
        }
        return $preachers;
    }

    public function all()
    {
        return $this->model->with('individual')->get();
    }
}
