<?php

namespace Bishopm\Churchnet\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Bishopm\Churchnet\Repositories\BaseRepository;
use Bishopm\Churchnet\Models\Circuit;

/**
 * Class EloquentCoreRepository
 *
 * @package Modules\Core\Repositories\Eloquent
 */
abstract class EloquentBaseRepository implements BaseRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model An instance of the Eloquent Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function valueBetween($field, $low, $high)
    {
        return $this->model->where($field, '>=', $low)->where($field, '<=', $high)->orderBy($field)->get();
    }

    public function sqlQuery($query)
    {
        return DB::select(DB::raw($query));
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->model->orderBy('created_at', 'DESC')->get();
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = 15)
    {
        return $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update($model, $data)
    {
        $model->update($data);
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function destroy($model)
    {
        return $model->delete();
    }


    /**
     * @inheritdoc
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByAttributes(array $attributes)
    {
        $query = $this->buildQueryByAttributes($attributes);

        return $query->first();
    }

    /**
     * @inheritdoc
     */
    public function getByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->buildQueryByAttributes($attributes, $orderBy, $sortOrder);

        return $query->get();
    }

    /**
     * Build Query to catch resources by an array of attributes and params
     * @param  array $attributes
     * @param  null|string $orderBy
     * @param  string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildQueryByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->model->query();
        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }
        if (null !== $orderBy) {
            $query->orderBy($orderBy, $sortOrder);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function findByMany(array $ids)
    {
        $query = $this->model->query();
        return $query->whereIn("id", $ids)->get();
    }

    public function allforcircuit($circuitnumber)
    {
        $circuit=Circuit::find($circuitnumber);
        return $this->model->with('circuit', 'society')->where('circuit_id', '=', $circuit->id)->get();
    }

    public function allforcircuitonly($circuitnumber)
    {
        $circuit=Circuit::find($circuitnumber);
        return $this->model->orderBy('tag', 'ASC')->where('circuit_id', '=', $circuit->id)->get();
    }

    public function findforcircuit($circuit, $id)
    {
        return $this->model->where('circuit_id', '=', $circuit)->where('id', '=', $id)->first();
    }

    public function allWithRelation($relation)
    {
        return $this->model->with($relation)->get();
    }
}
