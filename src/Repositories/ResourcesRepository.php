<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Resource;

class ResourcesRepository extends EloquentBaseRepository
{
    public function recents($num)
    {
        return $this->model->orderBy('created_at', 'DESC')->get()->take($num);
    }

    public function find($id)
    {
        return $this->model->with('tags')->find($id);
    }
}
