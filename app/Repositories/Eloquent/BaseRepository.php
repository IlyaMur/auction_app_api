<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\Criteria\CriteriaInterface;

abstract class BaseRepository implements BaseRepositoryInterface, CriteriaInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function withCriteria(...$criteria)
    {
        foreach (Arr::flatten($criteria) as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    public function all()
    {
        return $this->model->get();
    }

    protected function getModelClass()
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefined();
        }

        return app()->make($this->model());
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWhere($column, $value)
    {
        return $this->model
            ->where($column, $value)
            ->get();
    }

    public function findWhereFirst($column, $value)
    {
        return $this->model
            ->where($column, $value)
            ->firstOrFail();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $comment = $this->find($id);
        $comment->update($data);

        return $comment;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
