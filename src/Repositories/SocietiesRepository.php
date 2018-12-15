<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;

class SocietiesRepository extends EloquentBaseRepository
{
    public function findsociety($id)
    {
        return $this->model->with('services','users')->find($id);
    }

    public function dropdown()
    {
        return $this->model->orderBy('society', 'ASC')->select('id', 'society')->get();
    }

    public function allforcircuit($circuitnumber)
    {
        $circuit=Circuit::find($circuitnumber);
        return $this->model->with('circuit', 'services')->where('circuit_id', '=', $circuit->id)->get();
    }

    public function findBySlugForCircuitSlug($circuit, $slug)
    {
        $circuit_id = Circuit::where('slug', $circuit)->first()->id;
        return $this->model->with('services')->where('circuit_id', $circuit_id)->where('slug', $slug)->first();
    }

    public function create($data)
    {
        $society=$this->model->create($data);
        $society->slug=str_slug($society->society);
        $society->save();
        return $society;
    }

    /**
     * @inheritdoc
     */
    public function update($model, $data)
    {
        $model->update($data);
        $model->slug=str_slug($model->society);
        $model->save();
        return $model;
    }
}
