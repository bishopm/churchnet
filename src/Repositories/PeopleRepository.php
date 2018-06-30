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

    public function find($id)
    {
        $preacher=$this->model->where('id', $id)->first();
        $dum=$preacher->tags;
        return $preacher;
    }
}
