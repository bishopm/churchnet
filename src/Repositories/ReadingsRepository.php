<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;

class ReadingsRepository extends EloquentBaseRepository
{
    public function findByDesc($yr, $description)
    {
        $lection = $this->model->where('description', $description)->first();
        $data['description']=$description;
        $data['readings']=$lection->$yr;
        $data['year']=strtoupper($yr);
        return $data;
    }

    public function findByDate($yr, $sunday)
    {
        $sdate = substr($sunday, 5);
        $lection = $this->model->where('daterange', 'like', '%' . $sdate . '%')->get()->toArray();
        if (count($lection)==1) {
            $data['description']=$lection[0]['description'];
            $data['readings']=$lection[0][$yr];
            $data['year']=strtoupper($yr);
        } else {
            dd($sunday);
            $lection = $this->model->where('daterange', 'like', '%' . $sdate . '%')->where('priority', 1)->first();
            $data['description']=$lection->description;
            $data['readings']=$lection->$yr;
            $data['year']=strtoupper($yr);
        }
        return $data;
    }
}
