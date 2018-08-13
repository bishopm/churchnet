<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class CircuitsRepository extends EloquentBaseRepository
{
    public function withsocieties($id)
    {
        return $this->model->with('societies')->where('id',$id)->first();
    }
}
