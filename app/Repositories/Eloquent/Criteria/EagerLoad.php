<?php

namespace App\Repositories\Eloquent\Criteria;

use Illuminate\Support\Arr;
use App\Repositories\Contracts\Criteria\CriterionInterface;

class EagerLoad implements CriterionInterface
{
    protected $relationships;

    public function __construct(...$relationships)
    {
        $this->relationships = Arr::flatten($relationships);
    }

    public function apply($model)
    {
        return $model->with($this->relationships);
    }
}
