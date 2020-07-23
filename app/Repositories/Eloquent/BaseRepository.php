<?php


namespace App\Repositories\Eloquent;

use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;
use Illuminate\Support\Arr;


abstract class BaseRepository implements IBase, ICriteria
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function all()
    {
        return $this->model->get();
    }

    public function orderAll($col, $dir)
    {
        return $this->model->orderBy($col, $dir)->get();
    }

    public function groupByColumn($column)
    {
        return $this->all()->groupBy($column);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWhere($column, $value)
    {
        return $this->model->where($column, $value)->get();

    }

    public function findFirstBySeveralColumns(array $params)
    {
        foreach ($params as $key => $value) {
            $this->model->where($key, $value);
        }
        return $this->model->firstOrFail();

    }

    public function findFirst()
    {
        return $this->model->firstOrFail();
    }


    public function syncRelation($id, $relation, $data)
    {
        $this->model->find($id)->$relation()->sync($data);
    }

    public function detachRelation($id, $relation, $data='')
    {
        $this->model->find($id)->$relation()->detach($data);
    }

    public function deleteRelation($id, $relation)
    {
        $this->model->find($id)->$relation()->delete();
    }


    public function findWhereFirst($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage)
    {
        return $this->model->paginate($perPage);

    }

    public function create(array $data)
    {
        return $this->model->create($data);

    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }

    protected function getModelClass()
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefined();
        }

        return app()->make($this->model());

    }

    public function withCriteria(... $criteria)
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);

        }

        return $this;

    }

}
