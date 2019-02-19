<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class PreachersRepository extends EloquentBaseRepository
{
    public function allforcircuit($circuitnumber)
    {
        $circuit=Circuit::find($circuitnumber);
        return $this->model->with('circuit', 'society', 'person')->where('people.circuit_id', '=', $circuit->id)->where('people.active', 'yes')->orderBy('surname')->get();
    }
}
