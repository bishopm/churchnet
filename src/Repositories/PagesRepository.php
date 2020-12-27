<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Page;

class PagesRepository extends EloquentBaseRepository
{
    public function recents($num)
    {
        return $this->model->orderBy('created_at', 'DESC')->get()->take($num);
    }
}
