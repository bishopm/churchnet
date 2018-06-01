<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class ReadingsRepository extends EloquentBaseRepository
{
    public function locate($yr, $description)
    {
        $lection = $this->model->where('description', $description)->first();
        $data['description']=$description;
        $data['readings']=$lection->$yr;
        $data['year']=strtoupper($yr);
        return $data;
    }
}
